## Basic REST api for managing Tasks

### Local setup

1. Start containers by running:
```
make run
```
2. Build app
```
make build-dev
```

### Development

#### Standards (ECS, PHPStan,..)
Use script for checking all app standards
```
make check-all
```

#### Makefile
For effective work with host system. The config and more info in `Makefile` in project root directory.

#### API schema
Open API documentation in [openapi.json](openapi.json)

You can automatically generate OpenAPI schema with 
```
make generate-open-api-schema
```

