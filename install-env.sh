#!/bin/bash

valone(){
check_image=`aws ec2 describe-images --image-ids $1`

if [ -z "$check_image" ];
then
	echo "The provided image ID is not valid";
	echo "If you are passing 5 parameter the parameter should of below order"
	echo "1. AMI ID, 2. Key-Name, 3. Security Group, 4. Launch Configuration, 5. count (count cannot be less than zero), 6. IAM profile"
	echo "If you are passing 7 parameter the parameter should of below order"
	echo "1. AMI ID, 2. Key-Name, 3. Security Group, 4. Launch Configuration, 5. count (count cannot be less than zero), 6. Clinet Token, 7. Auto scaling group name, 8.Load Balancer name, 9. Iam profile name"
	exit 0;
fi

}

valtwo(){
check_key=`aws ec2 describe-key-pairs --key-name $1`

if [ -z "$check_key" ];
then
	echo "Not a valid Key";
	echo "If you are passing 5 parameter the parameter should of below order"
	echo "1. AMI ID, 2. Key-Name, 3. Security Group, 4. Launch Configuration, 5. count (count cannot be less than zero), 6. IAM profile"
	echo "If you are passing 7 parameter the parameter should of below order"
	echo "1. AMI ID, 2. Key-Name, 3. Security Group, 4. Launch Configuration, 5. count (count cannot be less than zero), 6. Clinet Token, 7. Auto scaling group name, 8.Load Balancer name, 9. IAM profile name"
	exit 0;
fi

}

valthree(){
check_security=`aws ec2 describe-security-groups --group-ids $1`

if [ -z "$check_security" ];
then
	echo "not a valid security group ID";
	echo "If you are passing 5 parameter the parameter should of below order"
	echo "1. AMI ID, 2. Key-Name, 3. Security Group, 4. Launch Configuration, 5. count (count cannot be less than zero), 6. IAM profile"
	echo "If you are passing 7 parameter the parameter should of below order"
	echo "1. AMI ID, 2. Key-Name, 3. Security Group, 4. Launch Configuration, 5. count (count cannot be less than zero), 6. Clinet Token, 7. Auto scaling group name, 8.Load Balancer name, 9. IAM profile name"
	exit 0;
fi

}

valfour(){
check_launch=`aws autoscaling describe-launch-configurations --launch-configuration-names $1`

if [ ! -z "$check_launch" ];
then
	echo "Launch configuration name already exists";
	echo "If you are passing 5 parameter the parameter should of below order"
	echo "1. AMI ID, 2. Key-Name, 3. Security Group, 4. Launch Configuration, 5. count (count cannot be less than zero), 6. IAM profile"
	echo "If you are passing 7 parameter the parameter should of below order"
	echo "1. AMI ID, 2. Key-Name, 3. Security Group, 4. Launch Configuration, 5. count (count cannot be less than zero), 6. Clinet Token, 7. Auto scaling group name, 8.Load Balancer name, 9. IAM profile name"
	exit 0;
fi

}


valfive(){

if [ $1 -lt 1 ];
then
	echo "count shpuld not be less than one";
	echo "If you are passing 5 parameter the parameter should of below order"
	echo "1. AMI ID, 2. Key-Name, 3. Security Group, 4. Launch Configuration, 5. count (count cannot be less than zero), 6. IAM profile"
	echo "If you are passing 7 parameter the parameter should of below order"
	echo "1. AMI ID, 2. Key-Name, 3. Security Group, 4. Launch Configuration, 5. count (count cannot be less than zero), 6. Clinet Token, 7. Auto scaling group name, 8.Load Balancer name, 9. IAM profile name"
	exit 0;
fi

}




valsix(){
check_client=`aws ec2 describe-instances --filters "Name=client-token,Values=$1" --query 'Reservations[*].Instances[].InstanceId'`

if [ ! -z "$check_client" ];
then
	echo "There are already instances present with this client token";
	echo "If you are passing 5 parameter the parameter should of below order"
	echo "1. AMI ID, 2. Key-Name, 3. Security Group, 4. Launch Configuration, 5. count (count cannot be less than zero), 6. IAM profile"
	echo "If you are passing 7 parameter the parameter should of below order"
	echo "1. AMI ID, 2. Key-Name, 3. Security Group, 4. Launch Configuration, 5. count (count cannot be less than zero), 6. Clinet Token, 7. Auto scaling group name, 8.Load Balancer name, 9. IAM profile name"
	exit 0;
fi
}

valseven(){
check_autoscaling=`aws autoscaling describe-auto-scaling-groups --auto-scaling-group-name $1`

if [ ! -z "$check_autoscaling" ];
then
	echo "There is a auto scaling group already present with this name";
	echo "If you are passing 5 parameter the parameter should of below order"
	echo "1. AMI ID, 2. Key-Name, 3. Security Group, 4. Launch Configuration, 5. count (count cannot be less than zero), 6. IAM profile"
	echo "If you are passing 7 parameter the parameter should of below order"
	echo "1. AMI ID, 2. Key-Name, 3. Security Group, 4. Launch Configuration, 5. count (count cannot be less than zero), 6. Clinet Token, 7. Auto scaling group name, 8.Load Balancer name, 9. IAM profile name"
	exit 0;
fi

}

valeight(){
check_loadbalancer=`aws elb describe-load-balancers --load-balancer-name $1`

if [ ! -z "$check_loadbalancer" ];
then
	echo "There is a load balancer group already present with this name";
	echo "If you are passing 5 parameter the parameter should of below order"
	echo "1. AMI ID, 2. Key-Name, 3. Security Group, 4. Launch Configuration, 5. count (count cannot be less than zero), 6. IAM profile"
	echo "If you are passing 7 parameter the parameter should of below order"
	echo "1. AMI ID, 2. Key-Name, 3. Security Group, 4. Launch Configuration, 5. count (count cannot be less than zero), 6. Clinet Token, 7. Auto scaling group name, 8.Load Balancer name, 9. IAM profile name"
	exit 0;
fi

}

if [ $# -eq 6 ]
then
echo "You have passed six parameter to the script"
echo "-----Check the parameter-----"
valone $1 
valtwo $2
valthree $3
valfour $4
valfive $5

echo "validation padded for six parameter"

#Launching 3 new instances, using the passed parameter
aws ec2 run-instances --image-id $1 --key-name $2 --security-group-ids $3 --client-token kr101 --instance-type t2.micro --user-data file://installapp.sh --placement AvailabilityZone=us-west-2b --count $5 --iam-instance-profile Name="$6"

#reteriving the instances ID with given clinet token in run instances command
instance_id=`aws ec2 describe-instances --filters "Name=client-token,Values=kr101" --query 'Reservations[*].Instances[].InstanceId'`

#Printing the instances ID's
echo $instance_id

#wait for the  launched instances to come to runniing state
aws ec2 wait instance-running --instance-ids $instance_id

#launch a load balancer  with HTTP listner
aws elb create-load-balancer --load-balancer-name itmo-544-kro --listeners Protocol=Http,LoadBalancerPort=80,InstanceProtocol=Http,InstancePort=80 --availability-zones us-west-2b --security-groups $3

#register the running instances with the load balancer
aws elb register-instances-with-load-balancer --load-balancer-name itmo-544-kro --instances $instance_id

#creat a launch configuration to attch  it to the auto scaling group
aws autoscaling create-launch-configuration --launch-configuration-name $4 --image-id $1  --key-name $2 --instance-type t2.micro --user-data file://installapp.sh --security-groups $3 --iam-instance-profile $6

#create a auto scaling group with minumum capacity as 0 and desired capacity as 1
aws autoscaling create-auto-scaling-group --auto-scaling-group-name webserver_demo --launch-configuration-name $4 --availability-zones us-west-2b --min-size 0 --max-size 5 --desired-capacity 0

#attach the running instances with the auto scaling group to over come the existing extra autoscaling problem now the desired capacity is increased to 4
aws autoscaling attach-instances --instance-ids $instance_id --auto-scaling-group-name webserver_demo

#attach the load balancer to the autoscaling group
aws autoscaling attach-load-balancers --load-balancer-names itmo-544-kro --auto-scaling-group-name webserver_demo

echo "creation succesful"

elif [ $# -eq 9 ]
then
valone $1 
valtwo $2
valthree $3
valfour $4
valfive $5
valsix $6
valseven $7
valeight $8
echo "validation passed for nine parameter"

#Launching 3 new instances, using the passed parameter
aws ec2 run-instances --image-id $1 --key-name $2 --security-group-ids $3 --client-token $6 --instance-type t2.micro --user-data file://installapp.sh --placement AvailabilityZone=us-west-2b --count $5 --iam-instance-profile Name="$9"

#reteriving the instances ID with given clinet token in run instances command
instance_id=`aws ec2 describe-instances --filters "Name=client-token,Values=$6" --query 'Reservations[*].Instances[].InstanceId'`

#Printing the instances ID's
echo $instance_id

#wait for the  launched instances to come to runniing state
aws ec2 wait instance-running --instance-ids $instance_id

#launch a load balancer  with HTTP listner
aws elb create-load-balancer --load-balancer-name $8 --listeners Protocol=Http,LoadBalancerPort=80,InstanceProtocol=Http,InstancePort=80 --availability-zones us-west-2b --security-groups $3

aws elb create-lb-cookie-stickiness-policy --load-balancer-name $8 --policy-name sticky-policy01 --cookie-expiration-period 3600

aws elb set-load-balancer-policies-of-listener --load-balancer-name $8 --load-balancer-port 80 --policy-names sticky-policy01

#register the running instances with the load balancer
aws elb register-instances-with-load-balancer --load-balancer-name $8 --instances $instance_id

#creat a launch configuration to attch  it to the auto scaling group
aws autoscaling create-launch-configuration --launch-configuration-name $4 --image-id $1  --key-name $2 --instance-type t2.micro --user-data file://installapp.sh --security-groups $3 --iam-instance-profile $9

#create a auto scaling group with minumum capacity as 0 and desired capacity as 1
aws autoscaling create-auto-scaling-group --auto-scaling-group-name $7 --launch-configuration-name $4 --availability-zones us-west-2b --min-size 0 --max-size 5 --desired-capacity 2

#attach the running instances with the auto scaling group to over come the existing extra autoscaling problem now the desired capacity is increased to 4
aws autoscaling attach-instances --instance-ids $instance_id --auto-scaling-group-name $7

#attach the load balancer to the autoscaling group
aws autoscaling attach-load-balancers --load-balancer-names $8 --auto-scaling-group-name $7

#crete a instance to process the image from raw bucket to finish bucket
aws ec2 run-instances --image-id $1 --key-name $2 --security-group-ids $3 --instance-type t2.micro --user-data file://installapptoprocessimage.sh --placement AvailabilityZone=us-west-2b --iam-instance-profile Name="$9"

echo "creation succesful"

else
echo "you have not passed exactly six/nine parameter";
exit 0;
fi

