#!/bin/bash

aws rds create-db-instance --db-instance-identifier itmo544-krose1-mysqldb --allocated-storage 5 --db-instance-class db.t2.micro --engine mysql --master-username controller --master-user-password controllerpass  --availability-zone us-west-2b --db-name school
db_instance_id=`aws rds describe-db-instances --query 'DBInstances[*].DBInstanceIdentifier'`
echo $db_instance_id
aws rds wait db-instance-available --db-instance-identifier $db_instance_id
echo "Data base created"
echo $db_instance_id
#aws rds delete-db-instance --skip-final-snapshot --db-instance-identifier $db_instance_id
#aws rds wait db-instance-deleted --db-instance-identifier $db_instance_id
#echo "instance deleted"
