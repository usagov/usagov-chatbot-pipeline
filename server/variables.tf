#######################################
variable "aws_profile" {
  type = string
  default = null
}

variable "aws_region" {
  description = "AWS Region"
  type    = string
  default = null
}

variable "availability_zone" {
  description = "Availablility Zone used vars subnet"
  type    = string
  default = null
}

variable "public_key" {
  type = string
  default = null
}

variable "ingress_host" {
  type = string
  default = null
}

variable "resource_prefix" {
  type = string
  default = "cbproto"
}

variable "system_ami_ownerid" {
  type = string
  default = "621876497272"
}
#######################################

### Hard-code AMI ids for accounts where we do not have ec2:DescribeImages permission
#   + custom_ami_id          = "ami-0ea6a58eec3a99944"
#   + system_primary_details = (known after apply)
#   + ubuntu_ami_id          = "ami-04f167a56786e4b09"
variable "ubuntu_2404_ami_id" {
    type = string
    default = "ami-04f167a56786e4b09"
}
variable "custom_ami_id" {
    type = string
    default = "ami-0ea6a58eec3a99944"
}

variable "system_ami_id" {
  type = string
  default = "ami-0ea6a58eec3a99944"
}

# shared instances
#default = "t3a.xlarge"        ###  4/16/--- / Linux base: .1504/hr / NO GPU / --> way too slow
#default = "c7i.4xlarge"       ### 16/32/--- / Linux base:  .714/hr / NO GPU / --> ok 18-32second queries
#default = "c7a.8xlarge"       ### 32/64/--- / Linux base: 1.642/hr / NO GPU / --> faster :)
#default = "c7i-flex.8xlarge"  ### 32/64/--- / Linux base: 1.357/hr / NO GPU /

# dedicated instances
#default = "c5d.4xlarge"  ### 16/32/200 / Linux base:  .768/hr / NO GPU /
#default = "g4ad.4xlarge" ###  6/64/600 / Linux base:  .864/hr / 1x AMD GPU
#default = "g4dn.2xlarge" ###  8/32/225 / Linux base:  .752/hr / 1x NVIDIA T4 GPU
#default = "g4dn.4xlarge" ### 16/64/225 / Linux base:  .204/hr / 1x NVIDIA GPU
#default = "g6.2xlarge"   ###  8/32/450 / Linux base:  .976/hr / 1x NVIDIA L4 GPU
#default = "g6.4xlarge"   ### 16/64/600 / Linux base: 1.323/hr / 1x NVIDIA L4 GPU

variable "system_instance_type_primary" {
  type = string
  default = "c7a.8xlarge"
}

variable "system_instance_type_secondary" {
  type = string
  default = "t3.small"
}
variable "system_volume_size" {
  type = number
  default = 50
}

variable "vpc_name" {
  type    = string
  default = "cbproto_vpc"
}

variable "vpc_cidr" {
  type    = string
  default = "10.0.0.0/16"
}

variable "private_subnets" {
  default = {
    "private_subnet_1" = 1
  }
}

variable "public_subnets" {
  default = {
    "public_subnet_1" = 1
  }
}

variable "sub_cidr" {
  description = "CIDR block for vars subnet"
  type    = string
  default = "10.0.250.0/24"
}

variable "sub_auto_ip" {
  description = "Set automatic IP assignment for vars subnet"
  type = string
  default = true
}
