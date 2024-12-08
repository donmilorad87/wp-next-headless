FROM node:22.11.0-alpine

WORKDIR /app

COPY ./package.json ./yarn.lock* ./

RUN yarn install

COPY ./app .

CMD ["yarn", "dev"]