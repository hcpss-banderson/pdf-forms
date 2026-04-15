FROM node:24-alpine

RUN mkdir /app

COPY ./public               /app/public
COPY ./src                  /app/src
COPY ./utils                /app/utils
COPY ./astro.config.mjs     /app/astro.config.mjs
COPY ./package.json         /app/package.json
COPY ./package-lock.json    /app/package-lock.json
COPY ./tsconfig.json        /app/tsconfig.json

WORKDIR /app

RUN npm install

CMD ["npm", "run", "dev"]
