
Final Submission

HEADS-UP:
--------

1. The Preseeded images are displayed only for the controller user (Database entry is made in such a way, only controller user has access to view the preseeded)

2. By default when application runs for first time the upload features is enabled for all user, this can be hided from the user by login as controller and navigate to controller, select OFF from dropdown and click on the submit. To enable the upload again ON is selected from drop down and submit is clicked.

3. When the upload is disabled the option disappears from screen for all screen (including controller user)

4. In admin page the restore database button is only displayed only after backup button is clicked

5. As per requirement the images are displayed in gallery as per user. Images can be selected and a pop appear displaying the selected image in larger view.

6. There are few PHP files created, which were not instructed in the assignment. These files help in the main PHP file in functioning. Kindly download the entire GIT repository while evaluating the code.

Credentials for users:

Controller:
Username: controller@iit.edu
Password: letmein
Krose1:
Username: krose1@hawk.iit.edu
Password: letmein
Professor:
Username: hajek@iit.edu
Password: letmein 



STEP 01- SETUP THE INFRASTRUCTURE
---------------------------------



Script to be executed:

install-app-env.sh

Parameter to be passed in the same order:

1.	Raw Bucket 
2.      Finish bucket
3.	Phone number - +16036744303 (the phone number should start with +1)

Command:

./install-app-env.sh raw-kro finish-kro [phone number]

Assumption:

The PHP scripts and other shell script are written with assumption that the bucket name will be raw-kro and finish-kro, kindly provide the same input.

The Db takes in Default security group it is assumed that default security group had 3306 port enabled



STEP 02- SETUP ENVIRONMENT
--------------------------


Script to be executed:

install-env.sh

Parameter to be passed in the same order:

1.	AMI ID--- please use ami-df05acbf
2.	key-name
3.	security-group
4.	launch-configuration
5.	count of the instance
6.	client token
7.	auto scaling group name
8.	load balancer name
9.	IAM profile name

Command:

./install-env.sh ami-df05acbf [Key-Name] [Security-Group] [Launch-Configuration] [Count] [Client-Token] [auto_scaling_Group_Name] [load_Balancer_Name] developer

Assumption:

The AWS cli command Is installed in your environment, the developer IAM profile has ec2, RDS, SQS, IAM, S3, SNS full access. 
The IAM role is named as developer. 
The security group has HTTP and SSH enabled


DESTROY ENVIRONMENT
-------------------


Script to be executed:

Destroy-env.sh

Parameter:

No parameter

command:

./destroy-env.sh

====================================================================================================================================================================================================
===============================================================================================================================================================================================



WeeK-10 Assignment:

For install-env.sh the aparmeter shoudl be passed in below order 1. AMI ID 2. Key-Name 3. Security Group 4. Launch Configuration 5. count (count cannot be less than zero) 6. Clinet Token 7. Auto scaling group name 8.Load Balancer name 9. Iam profile name


==================================================================================================================================================================================================
===============================================================================================================================================================================================



Week-12 Assignmnet:

LoginId Details for controller:
UserName: controller@iit.edu
Password: letmein

LoginID Details for Professor:
userNAme: jrh@iit.edu ( If this is not working try hajek@iit.edu)
Password: letmein

Student Login:
UserName: krose1@hawk.iit.edu
Password:letmein

A drop down is used in admin.php to change status of the upload feature. on/off should be clicked and then submit button should be clicked.

Few features have been added apart from the base requirment. 
The Upload features status ina table named credentials and have been retrived every time each page in the website is loaded, checkuploadenabled.php is used to validate the status of the upload feature.
Backup.php is used to backup the entire database and store it in the S3 bucket named databasebackup-kro
changestatus.php is used to change the status of upload features for all users (including controller himself) . 
Logout.php is used to clear session and divert to the login page
Restore.php is used to restore the entire database.
