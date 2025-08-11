# LLM Backend Server Setup w/ Terraform

## Plan

 `terraform init -backend-config=aws-gsa.tfbackend`
 
 `terraform plan -var-file=aws-gsa.tfvars -out aws-cbp.plan`

## Apply

`terraform apply -var-file=aws-gsa.tfvars aws-cbp.plan`
