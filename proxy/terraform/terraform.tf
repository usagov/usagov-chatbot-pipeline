terraform {
    required_version = ">= 1.10"
    required_providers {
      aws = {
        source = "hashicorp/aws"
        version = "~> 5.84"
      }
      random = {
        source = "hashicorp/random"
        version = "~> 3.6.0"
      }
      local = {
        source = "hashicorp/local"
        version = "~> 2.5"
      }
      http = {
        source = "hashicorp/http"
        version = "~> 3.4"
      }
      tls = {
        source = "hashicorp/tls"
        version = "~> 4.0"
      } 
    }
  backend "s3" {}
}
