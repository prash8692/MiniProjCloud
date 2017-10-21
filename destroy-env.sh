#!/bin/bash

load_balancer_name=`aws elb describe-load-balancers --query 'LoadBalancerDescriptions[*].LoadBalancerName'`

echo $load_balancer_name

launch_config_name=`aws autoscaling describe-launch-configurations --query 'LaunchConfigurations[].LaunchConfigurationName'`

echo $launch_config_name

auto_scaling_name=`aws autoscaling describe-auto-scaling-groups --query 'AutoScalingGroups[*].AutoScalingGroupName'`

echo $auto_scaling_name

arrauto=($auto_scaling_name)
for autoscalingname in "${arrauto[@]}";
do
	loadbalancer_auatoscaling=`aws autoscaling describe-auto-scaling-groups --auto-scaling-group-name $autoscalingname --query 'AutoScalingGroups[*].LoadBalancerNames'`;
	arrloadauto=($loadbalancer_auatoscaling);
	for autoloadname in "${arrloadauto[@]}";
	do
	aws autoscaling detach-load-balancers --load-balancer-names $autoloadname --auto-scaling-group-name $autoscalingname ; 
	done
done
echo "detached load balancer form auto scaling"




arrloadbalancer=($load_balancer_name)
for loadbalancer in "${arrloadbalancer[@]}";
do
	instance_attached_load=`aws elb describe-load-balancers --load-balancer-name $loadbalancer --query 'LoadBalancerDescriptions[*].Instances[].InstanceId'`;
	aws elb deregister-instances-from-load-balancer --load-balancer-name $loadbalancer --instances $instance_attached_load; 
done
echo "derigister instance from load balancer"





auto_scaling_name=`aws autoscaling describe-auto-scaling-groups --query 'AutoScalingGroups[*].AutoScalingGroupName'`
arrautoscaling=($auto_scaling_name)
for autoscaling in "${arrautoscaling[@]}";
do
	aws autoscaling update-auto-scaling-group --auto-scaling-group-name $autoscaling --min-size 0;
	instanceauto=`aws autoscaling describe-auto-scaling-groups --query 'AutoScalingGroups[*].Instances[].InstanceId'`;
	aws autoscaling detach-instances --instance-ids $instanceauto --auto-scaling-group-name $autoscaling --should-decrement-desired-capacity;
	aws autoscaling set-desired-capacity --auto-scaling-group-name $autoscaling --desired-capacity 0;
	aws ec2 terminate-instances --instance-ids $instanceauto;
	aws ec2 wait instance-terminated --instance-ids $instanceauto;
done
echo "terminated isntances from the auto scaling group"





arrloadbalancer=($load_balancer_name)
for loadbalancer in "${arrloadbalancer[@]}";
do 
	aws elb delete-load-balancer --load-balancer-name $loadbalancer;
done
echo "deleted load balancer"



auto_scaling_name=`aws autoscaling describe-auto-scaling-groups --query 'AutoScalingGroups[*].AutoScalingGroupName'`
arrautoscaling=($auto_scaling_name)
for autoscaling in "${arrautoscaling[@]}";
do
	aws autoscaling delete-auto-scaling-group --auto-scaling-group-name $autoscaling;
done
echo "deleted auto scaling group"



launch_config_name=`aws autoscaling describe-launch-configurations --query 'LaunchConfigurations[].LaunchConfigurationName'`
arrlaunchconfig=($launch_config_name)
for launchconfig in "${arrlaunchconfig[@]}";
do
	aws autoscaling delete-launch-configuration --launch-configuration-name $launchconfig;
done
echo "deleted launch configuration"

instance_id_running=`aws ec2 describe-instances --filters "Name=instance-state-code,Values=16" --query 'Reservations[*].Instances[].InstanceId'`

echo "Running instance ID's"

echo $instance_id_running
if [ -n "$instance_id_running" ]; 
then
aws ec2 terminate-instances --instance-ids $instance_id_running;

aws ec2 wait instance-terminated --instance-ids $instance_id_running;
echo "instances terminated";
else
echo "No Running instance"
fi


echo "clearing the infrastructure"

db_instance_id=`aws rds describe-db-instances --query 'DBInstances[*].DBInstanceIdentifier'`

#echo $db_instance_id

#aws rds delete-db-instance --skip-final-snapshot --db-instance-identifier $db_instance_id
#aws rds wait db-instance-deleted --db-instance-identifier $db_instance_id
#echo " DB instance deleted"

arrdb=($db_instance_id)
for dbname in "${arrdb[@]}";
do
echo $dbname
aws rds delete-db-instance --skip-final-snapshot --db-instance-identifier $dbname
aws rds wait db-instance-deleted --db-instance-identifier $dbname
echo "DB instance deleted"
done




#echo "deleting the bucket"
#aws s3api delete-object --bucket raw-kro --key switchonarex.jpg
#aws s3api delete-object --bucket raw-kro --key switchonarex.png
#aws s3api delete-bucket --bucket raw-kro --region us-west-2 
#aws s3api wait bucket-not-exists --bucket raw-kro
#aws s3api delete-bucket --bucket finish-kro --region us-west-2 
#aws s3api wait bucket-not-exists --bucket finish-kro
#aws s3api delete-bucket --bucket databasebackup-kro --region us-west-2
#aws s3api wait bucket-not-exists --bucket databasebackup-kro


#echo "buckets deleted"

echo "deleting quee"

listofquee=`aws sqs list-queues`

echo $listofquee

arrlist=($listofquee)

for listname in "${arrlist[@]}";
do
echo $listname

if [ $listname == "QUEUEURLS" ]
then

echo "the first value cannot be deleted"

else
aws sqs delete-queue --queue-url $listname

fi

done

echo "quee Cleared"

echo " Working on clearing the topic in SNS"

listoftopic=`aws sns list-topics`

echo $listoftopic

arrtopic=($listoftopic)

for listtopic in "${arrtopic[@]}";
do
echo $listtopic

if [ $listtopic == "TOPICS" ]
then

echo "the first value cannot be deleted"

else

aws sns delete-topic --topic-arn $listtopic

fi

done

echo "Cleared the topics"

echo "working on buckets"

BucketNames=`aws s3api list-buckets --query 'Buckets[].Name'`
arrBucketname=($BucketNames)
for Bucketname in "${arrBucketname[@]}";
do
        aws s3 rb s3://$Bucketname --force
done
echo "Deleted All Buckets"

echo "AWS env cleared"

