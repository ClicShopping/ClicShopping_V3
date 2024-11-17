# Comparison Table of all supported Language Models

| Model     |  Text  | Streaming |    Tools    | Images input | Images output | Speech to text  |
|-----------|:------:|:-----:|:-----------:|:------------:|:-------------:|:---------------:|
| Anthropic |   ✅    |   ✅   |      ✅      |              |               |                 |
| Mistral   |   ✅    |   ✅   |            |              |               |                 |
| Ollama    |   ✅    |   ✅   | Some models |              |               |                 |
| OpenAI    |   ✅    |   ✅   |      ✅      |      ✅       |       ✅       |        ✅      |

# Supported Vector Stores

| Store                     |
|---------------------------|
| AstraDB                   |
| Chroma                    |
| PostgreSQL (via Doctrine) |
| ElasticSearch             |
| Local File System         |
| Memory                    |
| Milvus                    |
| Qdrant                    |
| OpenSearch                |
| Redis                     |

# Supported embedding generators

| API - model    |  Vector length  |
|----------------|:---------------:|
| Mistral        |      1024       |
| Ollama         | model-dependent |
| OpenAI - small |      1536       |
| OpenAI - large |      3072       |
| OpenAI - ADA   |      1536       |
