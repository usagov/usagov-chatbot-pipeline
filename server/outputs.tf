
output "vpc_id" {
  value = aws_vpc.vpc.id
}

output "aws_profile" {
  value = var.aws_profile
}

output "aws_region" {
  value = var.aws_region
}
output "availability_zone" {
  value = var.availability_zone
}

output "system_primary_details" {
  value = "AMI: ${var.system_ami_id} / Instance Type: ${var.system_instance_type_primary} / EXPORT VPIP='${aws_instance.system_server_primary.public_ip}' / Hostname ${aws_instance.system_server_primary.public_ip}"
}
