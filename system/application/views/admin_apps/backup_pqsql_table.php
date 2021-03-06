-- Sequence: recommend_recommend_id_seq

-- DROP SEQUENCE recommend_recommend_id_seq;

CREATE SEQUENCE recommend_recommend_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 946
  CACHE 1;
ALTER TABLE recommend_recommend_id_seq OWNER TO kals;

-- -----------------------

-- Table: webpage

-- DROP TABLE webpage;

CREATE TABLE webpage
(
  webpage_id serial NOT NULL,
  uri character varying(2083) NOT NULL,
  title text,
  domain_id integer NOT NULL,
  CONSTRAINT webpage_pkey PRIMARY KEY (webpage_id)
)
WITH (OIDS=FALSE);
ALTER TABLE webpage OWNER TO kals;


-- Table: ci_sessions

-- DROP TABLE ci_sessions;

CREATE TABLE ci_sessions
(
  session_id character varying(40) NOT NULL DEFAULT 0,
  ip_address character varying(16) NOT NULL DEFAULT 0,
  user_agent character varying(50) NOT NULL,
  last_activity integer NOT NULL DEFAULT 0,
  user_data text,
  CONSTRAINT ci_sessions_pkey PRIMARY KEY (session_id)
)
WITH (OIDS=FALSE);
ALTER TABLE ci_sessions OWNER TO kals;

-- Table: "domain"

-- DROP TABLE "domain";

CREATE TABLE "domain"
(
  domain_id serial NOT NULL,
  host character varying(2083) NOT NULL,
  title text,
  CONSTRAINT domain_pk PRIMARY KEY (domain_id)
)
WITH (OIDS=FALSE);
ALTER TABLE "domain" OWNER TO kals;


-- Table: "group"

-- DROP TABLE "group";

CREATE TABLE "group"
(
  group_id serial NOT NULL,
  "name" text,
  domain_id integer NOT NULL,
  CONSTRAINT group_pkey PRIMARY KEY (group_id),
  CONSTRAINT group_group_id_key UNIQUE (group_id, domain_id)
)
WITH (OIDS=FALSE);
ALTER TABLE "group" OWNER TO kals;


-- Table: group2actor

-- DROP TABLE group2actor;

CREATE TABLE group2actor
(
  group2actor_id serial NOT NULL,
  group_id integer NOT NULL,
  actor_type_id integer NOT NULL,
  actor_id integer NOT NULL,
  CONSTRAINT group2actor_pkey PRIMARY KEY (group2actor_id),
  CONSTRAINT group2actor_group_id_fkey FOREIGN KEY (group_id)
      REFERENCES "group" (group_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT group2actor_group_id_key UNIQUE (group_id, actor_type_id, actor_id)
)
WITH (OIDS=FALSE);
ALTER TABLE group2actor OWNER TO kals;


-- Index: fki_

-- DROP INDEX fki_;

CREATE INDEX fki_
  ON group2actor
  USING btree
  (group_id);


-- Table: "user"

-- DROP TABLE "user";

CREATE TABLE "user"
(
  user_id serial NOT NULL,
  "name" text NOT NULL,
  email text,
  sex integer NOT NULL DEFAULT 0,
  photo boolean NOT NULL DEFAULT false,
  locale character varying(20),
  style text,
  "password" text,
  deleted boolean NOT NULL DEFAULT false,
  domain_id integer NOT NULL,
  CONSTRAINT user_pkey PRIMARY KEY (user_id)
)
WITH (OIDS=FALSE);
ALTER TABLE "user" OWNER TO kals;


-- --------------------------------

-- Table: anchor_text

-- DROP TABLE anchor_text;


CREATE TABLE anchor_text
(
  anchor_text_id serial NOT NULL,
  "text" text NOT NULL,
  indexed tsvector,
  segmented text,
  CONSTRAINT anchor_text_pkey PRIMARY KEY (anchor_text_id),
  CONSTRAINT anchor_text_text_key UNIQUE (text)
)
WITH (OIDS=FALSE);
ALTER TABLE anchor_text OWNER TO kals;


-- Table: scope

-- DROP TABLE scope;

CREATE TABLE scope
(
  scope_id serial NOT NULL,
  webpage_id integer NOT NULL,
  from_index integer NOT NULL,
  to_index integer NOT NULL,
  anchor_text_id integer NOT NULL,
  CONSTRAINT scope_pkey PRIMARY KEY (scope_id),
  CONSTRAINT scope_anchor_text_id_fkey FOREIGN KEY (anchor_text_id)
      REFERENCES anchor_text (anchor_text_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT scope_webpage_id_fkey FOREIGN KEY (webpage_id)
      REFERENCES webpage (webpage_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (OIDS=FALSE);
ALTER TABLE scope OWNER TO kals;


-- Table: policy

-- DROP TABLE policy;

CREATE TABLE policy
(
  policy_id serial NOT NULL,
  action_id integer NOT NULL,
  resource_type_id integer NOT NULL,
  resource_id integer NOT NULL,
  CONSTRAINT policy_pkey PRIMARY KEY (policy_id)
)
WITH (OIDS=FALSE);
ALTER TABLE policy OWNER TO kals;

-- Table: log

-- DROP TABLE log;

CREATE TABLE log
(
  log_id bigserial NOT NULL,
  user_id integer,
  log_timestamp timestamp with time zone NOT NULL DEFAULT now(),
  webpage_id integer,
  "action" integer,
  note text,
  user_ip text,
  CONSTRAINT log_pkey PRIMARY KEY (log_id)
)
WITH (OIDS=FALSE);
ALTER TABLE log OWNER TO kals;
COMMENT ON TABLE log IS '1=檢查登入成功	//記得要取得瀏覽器資料
2=檢查登入失敗
3=輸入登入成功
4=輸入登入失敗
5=內嵌登入成功
6=內嵌登入失敗
7=登出
8=註冊成功
9=註冊失敗
10=變更帳戶
11=變更密碼
12=瀏覽標註: 範圍
13=新增標註沒有建議:type;note
14=新增標註具有建議:type;note;recommend_id
15=修改標註:type:note
16=瀏覽討論
17=未登入者瀏覽
18=未登入者瀏覽討論
19=刪除標註:annotation_id
20=新增回應標註:type;topic_id;respond_id_list;note
21=修改回應標註:type;topic_id;respond_id_list;note
22=加入喜愛清單:被喜愛的annotation_id
23=移除喜愛清單:被移除的annotation_id
24=接受建議，沒有推薦:recommend_id
25=接受建議，有推薦:recommend_id
26=拒絕建議:recommend_id
27=發生錯誤:錯誤內容
28=查看說明';

CREATE TABLE annotation
(
  annotation_id serial NOT NULL,
  create_timestamp timestamp with time zone NOT NULL DEFAULT now(),
  update_timestamp timestamp with time zone NOT NULL DEFAULT now(),
  deleted boolean NOT NULL DEFAULT false,
  user_id integer NOT NULL,
  annotation_type_id integer NOT NULL DEFAULT 1,
  note text,
  note_index tsvector,
  topic_id integer,
  CONSTRAINT annotation_pkey PRIMARY KEY (annotation_id)
)
WITH (OIDS=FALSE);
ALTER TABLE annotation OWNER TO kals;

CREATE TABLE annotation2like
(
  annotation2like_id serial NOT NULL,
  annotation_id integer NOT NULL,
  user_id integer NOT NULL,
  create_time timestamp with time zone NOT NULL DEFAULT now(),
  canceled boolean NOT NULL DEFAULT false,
  update_time timestamp with time zone NOT NULL DEFAULT now(),
  create_timestamp timestamp with time zone,
  update_timestamp timestamp with time zone,
  CONSTRAINT annotation2like_pkey PRIMARY KEY (annotation2like_id),
  CONSTRAINT annotation2like_annotation_id_fkey FOREIGN KEY (annotation_id)
      REFERENCES annotation (annotation_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT annotation2like_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES "user" (user_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (OIDS=FALSE);
ALTER TABLE annotation2like OWNER TO kals;

-- Table: annotation2respond

-- DROP TABLE annotation2respond;

CREATE TABLE annotation2respond
(
  annotation2respond_id serial NOT NULL,
  annotation_id integer NOT NULL,
  respond_to integer NOT NULL,
  CONSTRAINT annotation2respond_pkey PRIMARY KEY (annotation2respond_id),
  CONSTRAINT annotation2respond_annotation_id_fkey FOREIGN KEY (annotation_id)
      REFERENCES annotation (annotation_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT annotation2respond_respond_to_fkey FOREIGN KEY (respond_to)
      REFERENCES annotation (annotation_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (OIDS=FALSE);
ALTER TABLE annotation2respond OWNER TO kals;

-- Index: fki_annotation2respond_respond_to

-- DROP INDEX fki_annotation2respond_respond_to;

CREATE INDEX fki_annotation2respond_respond_to
  ON annotation2respond
  USING btree
  (respond_to);

-- Table: annotation2scope

-- DROP TABLE annotation2scope;

CREATE TABLE annotation2scope
(
  annotation2scope_id serial NOT NULL,
  annotation_id integer NOT NULL,
  scope_id integer NOT NULL,
  CONSTRAINT annotation2scope_pkey PRIMARY KEY (annotation2scope_id),
  CONSTRAINT annotation2scope_annotation_id_fkey FOREIGN KEY (annotation_id)
      REFERENCES annotation (annotation_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT annotation2scope_scope_id_fkey FOREIGN KEY (scope_id)
      REFERENCES scope (scope_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (OIDS=FALSE);
ALTER TABLE annotation2scope OWNER TO kals;

-- Table: feature

-- DROP TABLE feature;

CREATE TABLE feature
(
  feature_id serial NOT NULL,
  annotation_id integer NOT NULL,
  feature_type_id integer NOT NULL,
  "value" text,
  CONSTRAINT feature_pkey PRIMARY KEY (feature_id),
  CONSTRAINT feature_annotation_id_fkey FOREIGN KEY (annotation_id)
      REFERENCES annotation (annotation_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (OIDS=FALSE);
ALTER TABLE feature OWNER TO kals;

-- Index: fki_feature_2_annotation

-- DROP INDEX fki_feature_2_annotation;

CREATE INDEX fki_feature_2_annotation
  ON feature
  USING btree
  (annotation_id);


-- Table: langvar

-- DROP TABLE langvar;


-- Table: notification

-- DROP TABLE notification;

CREATE TABLE notification
(
  notification_id serial NOT NULL,
  create_timestamp timestamp with time zone NOT NULL DEFAULT now(),
  "read" boolean NOT NULL DEFAULT false,
  update_timestamp timestamp with time zone NOT NULL DEFAULT now(),
  trigger_actor_type_id integer DEFAULT 1,
  trigger_actor_id integer,
  trigger_resource_type_id integer NOT NULL DEFAULT 3,
  trigger_resource_id integer NOT NULL,
  notification_type_id integer NOT NULL,
  association_user_id integer NOT NULL,
  CONSTRAINT notification_pkey PRIMARY KEY (notification_id),
  CONSTRAINT notification_association_user_id_fkey FOREIGN KEY (association_user_id)
      REFERENCES "user" (user_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT notification_trigger_actor_type_id_key UNIQUE (trigger_actor_type_id, trigger_actor_id, trigger_resource_type_id, trigger_resource_id, association_user_id, notification_type_id)
)
WITH (OIDS=FALSE);
ALTER TABLE notification OWNER TO kals;

-- Index: fki_notification_association_user_id

-- DROP INDEX fki_notification_association_user_id;

CREATE INDEX fki_notification_association_user_id
  ON notification
  USING btree
  (association_user_id);

-- Table: policy2actor

-- DROP TABLE policy2actor;

CREATE TABLE policy2actor
(
  policy2actor_id serial NOT NULL,
  policy_id integer NOT NULL,
  actor_type_id integer NOT NULL,
  actor_id integer NOT NULL,
  CONSTRAINT policy2actor_pkey PRIMARY KEY (policy2actor_id),
  CONSTRAINT policy2actor_policy_id_fkey FOREIGN KEY (policy_id)
      REFERENCES policy (policy_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (OIDS=FALSE);
ALTER TABLE policy2actor OWNER TO kals;

-- Table: recommend

-- DROP TABLE recommend;

CREATE TABLE recommend
(
  recommend_id integer NOT NULL,
  recommended_annotation_id integer NOT NULL,
  recommended_webpage_id integer,
  create_time timestamp with time zone NOT NULL DEFAULT now(),
  checked_time timestamp with time zone,
  accept boolean,
  recommend_by_annotation_id integer,
  deleted boolean NOT NULL DEFAULT false,
  create_timestamp timestamp with time zone,
  checked_timestamp timestamp with time zone,
  CONSTRAINT recommend_pkey PRIMARY KEY (recommend_id),
  CONSTRAINT recommend_recommend_by_annotation_id_fkey FOREIGN KEY (recommend_by_annotation_id)
      REFERENCES annotation (annotation_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT recommend_recommended_annotation_id_fkey FOREIGN KEY (recommended_annotation_id)
      REFERENCES annotation (annotation_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT recommend_recommended_webpage_id_fkey FOREIGN KEY (recommended_webpage_id)
      REFERENCES webpage (webpage_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (OIDS=FALSE);
ALTER TABLE recommend OWNER TO kals;

-- Table: score

-- DROP TABLE score;

CREATE TABLE score
(
  score_id serial NOT NULL,
  annotation_id integer NOT NULL,
  score_type_id integer NOT NULL DEFAULT 1,
  score numeric NOT NULL,
  CONSTRAINT score_pkey PRIMARY KEY (score_id),
  CONSTRAINT score_annotation_id_fkey FOREIGN KEY (annotation_id)
      REFERENCES annotation (annotation_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (OIDS=FALSE);
ALTER TABLE score OWNER TO kals;

-- Table: langvar

-- DROP TABLE langvar;

CREATE TABLE langvar
(
  langvar_id serial NOT NULL,
  webpage_id integer NOT NULL,
  langvar_type_id integer NOT NULL,
  function_variables text,
  threshold numeric,
  record numeric,
  weight numeric,
  updated boolean NOT NULL DEFAULT false,
  CONSTRAINT langvar_pkey PRIMARY KEY (langvar_id),
  CONSTRAINT langvar_webpage_id_fkey FOREIGN KEY (webpage_id)
      REFERENCES webpage (webpage_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT langvar_webpage_id_key UNIQUE (webpage_id, langvar_type_id)
)
WITH (OIDS=FALSE);
ALTER TABLE langvar OWNER TO kals;

SELECT setval('public.anchor_text_anchor_text_id_seq', 2853, true);
SELECT setval('public.annotation2like_annotation2like_id_seq', 574, true);
SELECT setval('public.recommend_recommend_id_seq', 946, true);
SELECT setval('public.annotation2respond_annotation2respond_id_seq', 381, true);
SELECT setval('public.annotation2scope_annotation2scope_id_seq', 4753, true);
SELECT setval('public.annotation_annotation_id_seq', 3502, true);
SELECT setval('public.domain_domain_id_seq', 270, true);
SELECT setval('public.feature_feature_id_seq', 2210, true);
SELECT setval('public.group2actor_group2actor_id_seq', 315, true);
SELECT setval('public.group_group_id_seq', 414, true);
SELECT setval('public.langvar_langvar_id_seq', 164, true);
SELECT setval('public.log_log_id_seq', 5878, true);
SELECT setval('public.notification_notification_id_seq', 1892, true);
SELECT setval('public.policy2actor_policy2actor_id_seq', 406, true);
SELECT setval('public.policy_policy_id_seq', 237, true);
SELECT setval('public.scope_scope_id_seq', 2560, true);
SELECT setval('public.score_score_id_seq', 8010, true);
SELECT setval('public.user_user_id_seq', 1693, true);
SELECT setval('public.webpage_webpage_id_seq', 1154, true);



-- ----------------------------

-- View: annotation2anchor_text

-- DROP VIEW annotation2anchor_text;

CREATE OR REPLACE VIEW annotation2anchor_text AS
 SELECT annotation.annotation_id, anchor_text.anchor_text_id, anchor_text.text, anchor_text.indexed, anchor_text.segmented
   FROM annotation
   JOIN annotation2scope USING (annotation_id)
   JOIN scope USING (scope_id)
   JOIN anchor_text USING (anchor_text_id);

ALTER TABLE annotation2anchor_text OWNER TO kals;
GRANT ALL ON TABLE annotation2anchor_text TO postgres;
GRANT SELECT ON TABLE annotation2anchor_text TO public;

-- View: annotation2like_count

-- DROP VIEW annotation2like_count;

CREATE OR REPLACE VIEW annotation2like_count AS
 SELECT annotation.annotation_id, count(annotation2like.user_id) AS like_count
   FROM annotation
   LEFT JOIN annotation2like ON annotation.annotation_id = annotation2like.annotation_id AND annotation2like.canceled = false AND annotation.user_id <> annotation2like.user_id
  GROUP BY annotation.annotation_id
  ORDER BY annotation.annotation_id;

ALTER TABLE annotation2like_count OWNER TO kals;
GRANT ALL ON TABLE annotation2like_count TO postgres;
GRANT SELECT ON TABLE annotation2like_count TO public;

-- View: annotation2respond_count

-- DROP VIEW annotation2respond_count;

CREATE OR REPLACE VIEW annotation2respond_count AS
 SELECT annotation.annotation_id, count(annotation2respond.annotation_id) AS responded_count
   FROM annotation
   LEFT JOIN annotation2respond ON annotation.annotation_id = annotation2respond.respond_to
  GROUP BY annotation.annotation_id;

ALTER TABLE annotation2respond_count OWNER TO kals;
GRANT ALL ON TABLE annotation2respond_count TO postgres;
GRANT SELECT ON TABLE annotation2respond_count TO public;

-- View: scope2length

-- DROP VIEW scope2length;

CREATE OR REPLACE VIEW scope2length AS
 SELECT scope.scope_id, scope.to_index - scope.from_index + 1 AS length
   FROM scope;

ALTER TABLE scope2length OWNER TO kals;

-- View: annotation2scope_length

-- DROP VIEW annotation2scope_length;

CREATE OR REPLACE VIEW annotation2scope_length AS
 SELECT annotation2scope.annotation_id, sum(scope2length.length) AS scope_length
   FROM annotation2scope
   JOIN scope2length USING (scope_id)
  GROUP BY annotation2scope.annotation_id;

ALTER TABLE annotation2scope_length OWNER TO kals;

-- View: annotation2score

-- DROP VIEW annotation2score;

CREATE OR REPLACE VIEW annotation2score AS
 SELECT annotation.annotation_id,
        CASE
            WHEN score.score IS NOT NULL THEN score.score
            ELSE 0::numeric
        END AS score
   FROM annotation
   LEFT JOIN score ON annotation.annotation_id = score.annotation_id AND score.score_type_id = 0
  ORDER BY annotation.annotation_id;

ALTER TABLE annotation2score OWNER TO kals;

-- View: annotation2topic_respond_count

-- DROP VIEW annotation2topic_respond_count;

CREATE OR REPLACE VIEW annotation2topic_respond_count AS
 SELECT annotation_topic.annotation_id, count(annotation_respond.annotation_id) AS topic_responded_count
   FROM annotation annotation_topic
   LEFT JOIN annotation annotation_respond ON annotation_respond.topic_id = annotation_topic.annotation_id AND annotation_respond.deleted IS FALSE AND annotation_respond.topic_id IS NOT NULL
  GROUP BY annotation_topic.annotation_id
  ORDER BY annotation_topic.annotation_id;

ALTER TABLE annotation2topic_respond_count OWNER TO kals;

-- View: annotation_consensus

-- DROP VIEW annotation_consensus;

CREATE OR REPLACE VIEW annotation_consensus AS
 SELECT main.annotation_id, count(consensus.annotation_id) AS count
   FROM ( SELECT annotation.annotation_id, annotation.user_id, annotation2scope.scope_id
           FROM annotation
      JOIN annotation2scope USING (annotation_id)
     WHERE annotation.topic_id IS NULL AND annotation.deleted IS FALSE) main
   LEFT JOIN ( SELECT annotation.annotation_id, annotation.user_id, annotation2scope.scope_id
           FROM annotation
      JOIN annotation2scope USING (annotation_id)
     WHERE annotation.topic_id IS NULL AND annotation.deleted IS FALSE) consensus ON main.scope_id = consensus.scope_id AND main.annotation_id <> consensus.annotation_id AND main.user_id <> consensus.user_id
  GROUP BY main.annotation_id
  ORDER BY main.annotation_id;

ALTER TABLE annotation_consensus OWNER TO kals;

-- View: webpage2annotation

-- DROP VIEW webpage2annotation;

CREATE OR REPLACE VIEW webpage2annotation AS
 SELECT DISTINCT scope.webpage_id, annotation.annotation_id
   FROM annotation
   JOIN annotation2scope ON annotation.annotation_id = annotation2scope.annotation_id
   JOIN scope ON annotation2scope.scope_id = scope.scope_id
  ORDER BY scope.webpage_id;

ALTER TABLE webpage2annotation OWNER TO kals;

-- -------------------------------------

