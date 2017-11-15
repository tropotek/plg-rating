


CREATE TABLE IF NOT EXISTS rating_question (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  profile_id INT UNSIGNED NOT NULL DEFAULT 0,
  text TEXT,
  total TINYINT(1) NOT NULL DEFAULT 0,                  -- Is this question added to the total
  help TEXT,
  order_by INT UNSIGNED NOT NULL DEFAULT 0,
  del TINYINT(1) NOT NULL DEFAULT 0,
  modified DATETIME NOT NULL,
  created DATETIME NOT NULL,
  PRIMARY KEY (id),
  INDEX (profile_id)
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS rating_value (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  question_id INT UNSIGNED NOT NULL DEFAULT 0,
  placement_id INT UNSIGNED NOT NULL DEFAULT 0,
  value INT UNSIGNED NOT NULL DEFAULT 1,
  modified DATETIME NOT NULL,
  created DATETIME NOT NULL,
  PRIMARY KEY (id),
  INDEX (placement_id, question_id),
  INDEX (placement_id)
) ENGINE=InnoDB;
