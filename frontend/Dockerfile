# Stage 1 - the build process
FROM node:10-alpine as build-deps
WORKDIR /usr/app
COPY package.json yarn.lock ./
RUN yarn
COPY . ./
RUN yarn build

# Stage 2 - the production environment
FROM nginx:alpine
RUN rm -rf /etc/nginx/conf.d

COPY conf /etc/nginx

COPY --from=build-deps /usr/app/build /usr/share/nginx/html
