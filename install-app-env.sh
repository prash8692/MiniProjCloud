#!/bin/bash


checkifmysqlisinstalled=`dpkg --get-selections | grep mysql`

if [ -z "$checkifmysqlisinstalled" ]; then
   sudo apt-get update -y
   sudo apt-get  install -y mysql-client-core-5.7
fi


aws rds create-db-instance --db-instance-identifier itmo544-krose1-mysqldb --allocated-storage 5 --db-instance-class db.t2.micro --engine mysql --master-username controller --master-user-password controllerpass  --availability-zone us-west-2b --db-name school
db_instance_id=`aws rds describe-db-instances --query 'DBInstances[*].DBInstanceIdentifier'`
echo $db_instance_id
aws rds wait db-instance-available --db-instance-identifier $db_instance_id
echo "Data base created"
echo $db_instance_id
db_instance_url=`aws rds describe-db-instances --query 'DBInstances[*].Endpoint[].Address'`
mysql --host=$db_instance_url --user='controller' --password='controllerpass' school << EOF
CREATE TABLE records(id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,email VARCHAR(255),phone VARCHAR(255),s3_raw_url VARCHAR(255),s3_finished_url VARCHAR(255),status INT(1),receipt VARCHAR(256));
create table credentials (ID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, userName VARCHAR(255) NOT NULL, userPass VARCHAR(255) NOT NULL, status varchar(255));
INSERT INTO credentials (userName,userPass,status) VALUES ('krose1@hawk.iit.edu','letmein','on');
INSERT INTO credentials (userName,userPass,status) VALUES ('hajek@iit.edu','letmein','on');
INSERT INTO credentials (userName,userPass,status) VALUES ('controller@iit.edu','letmein','on');
INSERT INTO records (email,phone,s3_raw_url,s3_finished_url,status,receipt) VALUES ('controller@iit.edu','6036744303','https://s3-us-west-2.amazonaws.com/raw-kro/eartrumpet.png','https://s3-us-west-2.amazonaws.com/finish-kro/eartrumpet-bw.png',1,'Preseeded');
INSERT INTO records (email,phone,s3_raw_url,s3_finished_url,status,receipt) VALUES ('controller@iit.edu','6036744303','https://s3-us-west-2.amazonaws.com/raw-kro/Knuth.jpg','https://s3-us-west-2.amazonaws.com/finish-kro/Knuth-bw.jpg',1,'Preseeded');
INSERT INTO records (email,phone,s3_raw_url,s3_finished_url,status,receipt) VALUES ('controller@iit.edu','6036744303','https://s3-us-west-2.amazonaws.com/raw-kro/mountain.jpg','https://s3-us-west-2.amazonaws.com/finish-kro/mountain-bw.jpg',1,'Preseeded');
commit;
EOF


aws rds create-db-instance-read-replica --db-instance-identifier itmo544-krose1-mysqldb-readonly --source-db-instance-identifier itmo544-krose1-mysqldb --db-instance-class db.t2.micro --availability-zone us-west-2b

aws rds wait db-instance-available --db-instance-identifier itmo544-krose1-mysqldb-readonly

echo "database readonly created"


#Create SNS Topic 
topic_arn_name=`aws sns create-topic --name krose-topic`

#create Subscribe topic
#aws sns subscribe --topic-arn $topic_arn_name --protocol email --notification-endpoint kamalasekar091@gmail.com
aws sns subscribe --topic-arn $topic_arn_name --protocol sms --notification-endpoint +16036744303

aws sns subscribe --topic-arn $topic_arn_name --protocol sms --notification-endpoint $3

# create an S3 bucket
aws s3api create-bucket --bucket $1 --region us-west-2 --create-bucket-configuration LocationConstraint=us-west-2

aws s3api create-bucket --bucket $2 --region us-west-2 --create-bucket-configuration LocationConstraint=us-west-2

#wait for bucket availability
aws s3api wait bucket-exists --bucket $1
echo "$1 created"

aws s3api wait bucket-exists --bucket $2

echo "$2 created"

aws s3api create-bucket --bucket databasebackup-kro --region us-west-2 --create-bucket-configuration LocationConstraint=us-west-2

aws s3api wait bucket-exists --bucket databasebackup-kro

echo "dtabaseBackup created"

#create queue
#create queue
aws sqs create-queue --queue-name kro-queue

presentworkingdirectory=`pwd`

if [ ! -f $presentworkingdirectory/eartrumpet-bw.png ]; 
then
    wget https://dl.dropboxusercontent.com/u/68320238/cloud/eartrumpet-bw.png
fi

if [ ! -f $presentworkingdirectory/eartrumpet.png ]; 
then
    wget https://dl.dropboxusercontent.com/u/68320238/cloud/eartrumpet.png
fi

if [ ! -f $presentworkingdirectory/Knuth-bw.jpg ]; 
then
    wget https://dl.dropboxusercontent.com/u/68320238/cloud/Knuth-bw.jpg
fi

if [ ! -f $presentworkingdirectory/Knuth.jpg ]; 
then
    wget https://dl.dropboxusercontent.com/u/68320238/cloud/Knuth.jpg
fi

if [ ! -f $presentworkingdirectory/mountain-bw.jpg ]; 
then
    wget https://dl.dropboxusercontent.com/u/68320238/cloud/mountain-bw.jpg
fi

if [ ! -f $presentworkingdirectory/mountain.jpg ]; 
then
    wget https://dl.dropboxusercontent.com/u/68320238/cloud/mountain.jpg
fi

if [ ! -f $presentworkingdirectory/IIT-logo.png ];
then
    wget https://dl.dropboxusercontent.com/u/68320238/cloud/IIT-logo.png
fi

#pushing the images into raw bucket

aws s3 cp eartrumpet.png s3://$1/ --grants read=uri=http://acs.amazonaws.com/groups/global/AllUsers
aws s3 cp Knuth.jpg s3://$1/ --grants read=uri=http://acs.amazonaws.com/groups/global/AllUsers
aws s3 cp mountain.jpg s3://$1/ --grants read=uri=http://acs.amazonaws.com/groups/global/AllUsers
aws s3 cp IIT-logo.png s3://$1/ --grants read=uri=http://acs.amazonaws.com/groups/global/AllUsers

# pushing the image into finish bucket 

aws s3 cp eartrumpet-bw.png s3://$2/ --grants read=uri=http://acs.amazonaws.com/groups/global/AllUsers
aws s3 cp Knuth-bw.jpg s3://$2/ --grants read=uri=http://acs.amazonaws.com/groups/global/AllUsers
aws s3 cp mountain-bw.jpg s3://$2/ --grants read=uri=http://acs.amazonaws.com/groups/global/AllUsers


#Create a instance to process the image pushed in raw-kro

#aws ec2 run-instances --image-id ami-09b31569 --key-name inclassnew --security-group-ids sg-6a60ad13 --instance-type t2.micro --placement AvailabilityZone=us-west-2b --iam-instance-profile Name="developer" --user-data file://installapptoprocessimage.sh
