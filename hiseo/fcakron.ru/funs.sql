DELIMITER $$ ;
CREATE FUNCTION point_1(t INT, m INT)
  RETURNS int
  DETERMINISTIC
  SQL SECURITY INVOKER
  COMMENT 'calculates point first team'
BEGIN
    DECLARE i1, i2, p INT;
	SELECT goal_1 into i1, goal_2 into i2 FROM meet WHERE code=t and tourney=t;
    if i1 > i2 then set p=2;
    if i1 = i2 then set p=1;
    if i1 < i2 then set p=0;
    RETURN p;
END
$$
DELIMITER ; $$