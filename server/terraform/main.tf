
data "aws_region" "current" {}

resource "aws_key_pair" "deployer" {
  key_name   = "deployer-key"
  public_key = var.public_key
}

#Define the VPC
resource "aws_vpc" "vpc" {
  cidr_block = var.vpc_cidr

  tags = {
    Name        = "${var.resource_prefix}_vpc"
    Terraform   = "true"
  }
}

#Deploy the public subnets
resource "aws_subnet" "public_subnets" {
  for_each                = var.public_subnets
  vpc_id                  = aws_vpc.vpc.id
  cidr_block              = cidrsubnet(var.vpc_cidr, 8, each.value + 100)
  availability_zone = var.availability_zone
  map_public_ip_on_launch = true

  tags = {
    Name      = each.key
    Terraform = "true"
  }
}

#Create route tables for public and private subnets
resource "aws_route_table" "public_route_table" {
  vpc_id = aws_vpc.vpc.id

  route {
    cidr_block = "0.0.0.0/0"
    gateway_id = aws_internet_gateway.internet_gateway.id
  }
  tags = {
    Name      = "${var.resource_prefix}_public_rtb"
    Terraform = "true"
  }
}

#Create route table associations
resource "aws_route_table_association" "public" {
  depends_on     = [aws_subnet.public_subnets]
  route_table_id = aws_route_table.public_route_table.id
  for_each       = aws_subnet.public_subnets
  subnet_id      = each.value.id
}

#Create Internet Gateway
resource "aws_internet_gateway" "internet_gateway" {
  vpc_id = aws_vpc.vpc.id
  tags = {
    Name = "${var.resource_prefix}_igw"
  }
}

#Create EIP for NAT Gateway
resource "aws_eip" "nat_gateway_eip" {
  domain     = "vpc"
  depends_on = [aws_internet_gateway.internet_gateway]
  tags = {
    Name = "${var.resource_prefix}_igw_eip"
  }
}

#Create NAT Gateway
resource "aws_nat_gateway" "nat_gateway" {
  depends_on    = [aws_subnet.public_subnets]
  allocation_id = aws_eip.nat_gateway_eip.id
  subnet_id     = aws_subnet.public_subnets["public_subnet_1"].id
  tags = {
    Name = "${var.resource_prefix}_nat_gateway"
  }
}

resource "aws_eip" "system_eip" {
  domain     = "vpc"
  depends_on = [aws_internet_gateway.internet_gateway]
  tags = {
    Name = "system_eip"
  }
}

# Terraform Data Block - Ubuntu Server 24.04 LTS (HVM), SSD Volume Type, x86_64
#data "aws_ami" "ubuntu_2404" {
#  most_recent = true
#
#  filter {
#    name   = "name"
#    values = ["ubuntu/images/hvm-ssd-gp3/ubuntu-noble-24.04-amd64-server-20250305"]
#  }
#
#  filter {
#    name   = "virtualization-type"
#    values = ["hvm"]
#  }
#
#  owners = ["099720109477"] # Canonical
#}

# Terraform Data Block - Custom AMI v2, pre-installed requirements
#data "aws_ami" "system_ami" {
#  most_recent = true
#
#  filter {
#    name   = "name"
#    values = ["${var.system_ami_name_primary}"]
#  }
#
#  filter {
#    name   = "virtualization-type"
#    values = ["hvm"]
#  }
#
#  owners = ["${var.system_ami_ownerid}"] # straypacket
#}


# EC2 Primary instance in Public Subnet
resource "aws_instance" "system_server_primary" {
  ami           = var.system_ami_id
  #ami           = data.aws_ami.system_ami.id
  instance_type = "${var.system_instance_type_primary}"

  root_block_device {
   volume_size = var.system_volume_size
  }

  ### permission needed: ec2:ImportKeyPair
  key_name      = aws_key_pair.deployer.key_name
  subnet_id     = aws_subnet.public_subnets["public_subnet_1"].id
  vpc_security_group_ids = [aws_security_group.allow_standard.id,
                            aws_security_group.allow_llm.id,
                            aws_security_group.allow_egress.id] # Associate the security groups
  tags = {
    Name = "${var.resource_prefix}"
  }

  user_data = <<-EOF
#!/bin/bash
echo "${var.system_ami_id}"
EOF
}

resource "aws_eip_association" "eip_assoc" {
  allocation_id = aws_eip.system_eip.id
  instance_id = aws_instance.system_server_primary.id
}

resource "aws_security_group" "allow_egress" {
  name = "all_outbound"
  description = "Allow all outbound"
  vpc_id = aws_vpc.vpc.id
  tags = {
    Name = "all_outbound"
    Purpose = "Allow outbound traffic"
  }
}

resource "aws_vpc_security_group_egress_rule" "system-sec-group-egress" {
    security_group_id = aws_security_group.allow_egress.id
    ip_protocol = -1
    cidr_ipv4 = "0.0.0.0/0"
}



resource "aws_security_group" "allow_standard" {
  name = "standard_inbound"
  description = "Allow inbound for http/s, ssh"
  vpc_id = aws_vpc.vpc.id
  tags = {
    Name = "allow_standard"
    Purpose = "Standard Port Access"
  }
}

resource "aws_security_group" "allow_llm" {
  name        = "allow_llm"
  description = "Allows LLM traffic"
  vpc_id = aws_vpc.vpc.id
  tags = {
    Name = "allow_llm"
  }
}

resource "aws_vpc_security_group_ingress_rule" "system-sec-group-ingress-http" {
    count = length(var.ingress_cidr_blocks)
    security_group_id = aws_security_group.allow_standard.id
    from_port = 80
    to_port = 80
    ip_protocol = "tcp"
    cidr_ipv4 = var.ingress_cidr_blocks[count.index].cidr_block
}
resource "aws_vpc_security_group_ingress_rule" "system-sec-group-ingress-https" {
    count = length(var.ingress_cidr_blocks)
    security_group_id = aws_security_group.allow_standard.id
    from_port = 443
    to_port = 443
    ip_protocol = "tcp"
    cidr_ipv4 = var.ingress_cidr_blocks[count.index].cidr_block
}
resource "aws_vpc_security_group_ingress_rule" "system-sec-group-ingress-ssh" {
    count = length(var.ingress_cidr_blocks)
    security_group_id = aws_security_group.allow_standard.id
    from_port = 22
    to_port = 22
    ip_protocol = "tcp"
    cidr_ipv4 = var.ingress_cidr_blocks[count.index].cidr_block
}

resource "aws_vpc_security_group_ingress_rule" "system-sec-group-ingress-proxy-admin" {
    count = length(var.ingress_cidr_blocks)
    security_group_id = aws_security_group.allow_standard.id
    from_port = 81
    to_port = 81
    ip_protocol = "tcp"
    cidr_ipv4 = var.ingress_cidr_blocks[count.index].cidr_block
}

#resource "aws_vpc_security_group_ingress_rule" "system-sec-group-ingress-chroma" {
#    count = length(var.ingress_cidr_blocks)
#    security_group_id = aws_security_group.allow_llm.id
#    from_port = 8000
#    to_port = 8000
#    ip_protocol = "tcp"
#    cidr_ipv4 = var.ingress_cidr_blocks[count.index].cidr_block
#}

#resource "aws_vpc_security_group_ingress_rule" "system-sec-group-ingress-ollama" {
#    count = length(var.ingress_cidr_blocks)
#    security_group_id = aws_security_group.allow_llm.id
#    from_port = 11434
#    to_port = 11434
#    ip_protocol = "tcp"
#    cidr_ipv4 = var.ingress_cidr_blocks[count.index].cidr_block
#}

#resource "aws_vpc_security_group_ingress_rule" "system-sec-group-ingress-openweb" {
#    count = length(var.ingress_cidr_blocks)
#    security_group_id = aws_security_group.allow_llm.id
#    from_port = 8080
#    to_port = 8080
#    ip_protocol = "tcp"
#    cidr_ipv4 = var.ingress_cidr_blocks[count.index].cidr_block
#}

#resource "aws_ebs_volume" "system_volume_srv" {
#  availability_zone = var.availability_zone
#  size = 40 # Replace with your desired volume size in GB
#  tags = {
#    Name = "${var.resource_prefix} srv" # Optional: Add tags for identification
#  }
#}
### once we know the proper device_name we can attach.
### but we currently still need to manually format/mount
#resource "aws_volume_attachment" "system_volume_srv_attachment" {
#  volume_id  = aws_ebs_volume.system_volume_srv.id
#  instance_id = aws_instance.system.id
#  device_name = "/dev/xvdbg"
#}
