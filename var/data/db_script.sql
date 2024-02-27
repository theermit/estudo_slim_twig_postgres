-- Database: agenda

-- DROP DATABASE IF EXISTS agenda;

CREATE DATABASE agenda
    WITH
    OWNER = sa
    ENCODING = 'UTF8'
    LC_COLLATE = 'pt_BR.UTF-8'
    LC_CTYPE = 'pt_BR.UTF-8'
    LOCALE_PROVIDER = 'libc'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1
    IS_TEMPLATE = False;

-- Table: public.Session

-- DROP TABLE IF EXISTS public."Session";

CREATE TABLE IF NOT EXISTS public."Session"
(
    "Session_Id" character varying(255) COLLATE pg_catalog."default" NOT NULL,
    "Session_Expires" timestamp without time zone NOT NULL,
    "Session_Data" text COLLATE pg_catalog."default",
    CONSTRAINT "Session_pkey" PRIMARY KEY ("Session_Id")
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public."Session"
    OWNER to sa;


-- Table: public.usuario

-- DROP TABLE IF EXISTS public.usuario;

CREATE TABLE IF NOT EXISTS public.usuario
(
    id bigint NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1 ),
    nome character varying(500) COLLATE pg_catalog."default" NOT NULL,
    email character varying(500) COLLATE pg_catalog."default" NOT NULL,
    senha character varying(255) COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT usuario_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.usuario
    OWNER to sa;

-- Table: public.contato

-- DROP TABLE IF EXISTS public.contato;

CREATE TABLE IF NOT EXISTS public.contato
(
    id bigint NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1 ),
    nome character varying(255) COLLATE pg_catalog."default" NOT NULL,
    telefone character varying(255) COLLATE pg_catalog."default" NOT NULL,
    id_usuario bigint NOT NULL,
    CONSTRAINT fk_contato FOREIGN KEY (id_usuario)
        REFERENCES public.usuario (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.contato
    OWNER to sa;
-- Index: fki_fk_contato

-- DROP INDEX IF EXISTS public.fki_fk_contato;

CREATE INDEX IF NOT EXISTS fki_fk_contato
    ON public.contato USING btree
    (id_usuario ASC NULLS LAST)
    TABLESPACE pg_default;