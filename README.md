# PDF Forms

Clone the repo and run:

```bash
# Install npm dependencies
docker run -v $(pwd)/astro:/app --workdir=/app node:24-alpine npm i

# Put your license key in config/licensekey.txt

# Launch containers
docker compose up -d --build
```

Visit http://localhost:8888
