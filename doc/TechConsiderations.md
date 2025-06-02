## Considerations for proof of concept implementation

1. This information is meant to be used from a DevOps perspective, therefore tools and tactics will be command-line-centric (MacOS, Linux, WSL2 should all work similarly)
1. The model and environment must be small enough to reside on a standard USA.gov developer workstation
1. The data must be kept local-only, meaning no online AI services should be used. The local-only moniker also applies to a server implementation, as the backend will not make queries outside of the LLM+VDB residing on the server.
1. Everything should be open source (or at least free of charge at this stage)
1. No accounts should be neccesary for any external tools or data used in the POC
