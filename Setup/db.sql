create table log
(
	document_uri        varchar(2048) default ''    not null,
	referrer            varchar(2048) default ''    not null,
	violated_directive  text                        not null,
	effective_directive text                        not null,
	original_policy     text                        not null,
	disposition         varchar(50)   default ''    not null,
	blocked_uri         varchar(2048) default ''    not null,
	line_number         int           default 0     not null,
	columns_number      int           default 0     not null,
	source_file         varchar(2048) default ''    not null,
	status_code         int           default 0     not null,
	script_sample       varchar(255)  default ''    not null,
	http_user_agent     varchar(255)  default ''    not null,
	tstamp              datetime      default now() null
)
	comment 'CSP violations';
