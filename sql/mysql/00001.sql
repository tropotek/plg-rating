




alter table rating_question change profile_id course_id int unsigned default 0 not null;
drop index profile_id on rating_question;
create index course_id on rating_question (course_id);

