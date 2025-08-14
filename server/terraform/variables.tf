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

variable "resource_prefix" {
  type = string
  default = "cbp"
}

#######################################

### Hard-code AMI ids for accounts where we do not have ec2:DescribeImages permission
variable "ubuntu_2404_ami_id" {
    type = string
    default = "ami-04f167a56786e4b09" ### x86/amd/64 image id
    #default = "ami-0fb8a63da4a19607d"  ### arm64 image id
}

variable "system_ami_id" {
  type = string
    default = "ami-04f167a56786e4b09" ### x86/amd/64 image id
    #default = "ami-0fb8a63da4a19607d"  ### arm64 image id
}

variable "system_instance_type_primary" {
  type = string
  #default = "c7i.4xlarge"
  default = "c7i.8xlarge"
}

variable "system_instance_type_secondary" {
  type = string
  #default = "c7i.4xlarge"
  default = "c7i.8xlarge"
}

variable "system_volume_size" {
  type = number
  default = 50
}

variable "vpc_name" {
  type    = string
  default = "cbp_vpc"
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

variable "ingress_cidr_blocks" {
  description = "List of CIDR blocks for ingress traffic"
  type = list(object({
    cidr_block = string
  }))
  default = [
    # GSA IPv4 blocks and addresses:
    { cidr_block = "98.97.154.43/32" },
    { cidr_block = "159.142.0.0/16" },
    { cidr_block = "136.226.4.0/23" },
    { cidr_block = "136.226.6.0/23" },
    { cidr_block = "136.226.8.0/23" },
    { cidr_block = "136.226.10.0/23" },
    { cidr_block = "136.226.12.0/23" },
    { cidr_block = "136.226.14.0/23" },
    { cidr_block = "136.226.16.0/23" },
    { cidr_block = "136.226.18.0/23" },
    { cidr_block = "136.226.20.0/23" },
    { cidr_block = "136.226.22.0/24" },
    { cidr_block = "165.225.3.0/24" },
    { cidr_block = "165.225.46.0/24" },
    { cidr_block = "165.225.48.0/24" },
  ]
}

# variable "ingress_cidr6_blocks" {
#   description = "List of CIDR blocks for ingress traffic (IPv6)"
#   type = list(object({
#     cidr_block = string
#   }))
#   default = [
#     # GSA IPv6 blocks and addresses:
#     { cidr_block = "2607:6540:2000:800::/64" },
#     { cidr_block = "2607:6540:2700:800::/64" },
#     { cidr_block = "2620:0:150:3447:5d77:841e:bc01:5bf4/128" },
#     { cidr_block = "2620:0:150:3447:8fca:87e5:da51:4b7b/128" },
#     { cidr_block = "2620:0:150:3447:daad:6c9f:fb78:b689/128" },
#     { cidr_block = "2620:0:150:3447:faf0:b47d:a67d:5bad/128" },
#     { cidr_block = "2620:0:150:3447:6df7:3fdc:64b4:1b83/128" },
#     { cidr_block = "2620:0:150:3447:1196:7f61:5aab:fd8f/128" },
#     { cidr_block = "2620:0:150:3447:2f92:e6a1:efaa:fb98/128" },
#     { cidr_block = "2620:0:150:3447:2772:3f17:a0eb:8566/128" },
#     { cidr_block = "2620:0:150:4029:896d:2ddc:8450:efe9/128" },
#     { cidr_block = "2620:0:150:4029:de4b:dd85:31cc:6f55/128" },
#     { cidr_block = "2620:0:150:4029:a5df:d6e0:fc6c:e16b/128" },
#     { cidr_block = "2620:0:150:4029:da15:7695:c137:7767/128" },
#     { cidr_block = "2620:0:150:4029:a5bc:cc3f:55f9:6458/128" },
#     { cidr_block = "2620:0:150:4029:b8c7:f0d0:941e:de07/128" },
#     { cidr_block = "2620:0:150:4029:c9cc:5ce:ec04:eb30/128" },
#     { cidr_block = "2620:0:150:4029:f30c:dc1f:6722:b47b/128" },

#     # ZScaler "edge" addresses; sometimes GSA linux clients come through on these
#     { cidr_block = "2605:4300:1511::/48" },
#     { cidr_block = "2605:4300:1512::/48" },
#     { cidr_block = "2605:4300:1611::/48" },
#     { cidr_block = "2605:4300:1612::/48" },
#     { cidr_block = "2605:4300:1711::/48" },
#     { cidr_block = "2605:4300:2c11::/48" },
#     { cidr_block = "2605:4300:2c12::/48" },
#   ]
# }

#
# AWS Instance Type - info/pricing 6/04/25
#
# shared instances
#x86/amd
#default = "t3a.xlarge"        ###  4/16/--- / Linux base: .1504/hr / NO GPU / --> way too slow
#default = "c7i.4xlarge"       ### 16/32/--- / Linux base:  .714/hr / NO GPU / --> ok 18-32second queries
#default = "c7a.8xlarge"       ### 32/64/--- / Linux base: 1.642/hr / NO GPU / --> faster :)
#default = "c7i-flex.8xlarge"  ### 32/64/--- / Linux base: 1.357/hr / NO GPU /
#arm
#default = "c7g.4xlarge"        ### 16/32/--- / Linux base:  .579/hr / NO GPU / --> ok 18-32second queries
#default = "c7g.2xlarge"        ### 16/32/--- / Linux base:  .290/hr / NO GPU / --> ?? testing

# dedicated instances
#default = "c5d.4xlarge"  ### 16/32/200 / Linux base:  .768/hr / NO GPU /
#default = "g4ad.4xlarge" ###  6/64/600 / Linux base:  .864/hr / 1x AMD GPU
#default = "g4dn.2xlarge" ###  8/32/225 / Linux base:  .752/hr / 1x NVIDIA T4 GPU
#default = "g4dn.4xlarge" ### 16/64/225 / Linux base:  .204/hr / 1x NVIDIA GPU
#default = "g6.2xlarge"   ###  8/32/450 / Linux base:  .976/hr / 1x NVIDIA L4 GPU
#default = "g6.4xlarge"   ### 16/64/600 / Linux base: 1.323/hr / 1x NVIDIA L4 GPU
