# POKEDEX LARAVEL
## Dockerfile
### Usar PHP-FPM con Nginx como base
FROM php:8.2-fpm AS base

### Instalar dependencias del sistema necesarias para Laravel y Nginx
RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    git \
    unzip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

### Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

### Establecer el directorio de trabajo
WORKDIR /var/www/html

### Copiar los archivos del proyecto (excluyendo los no necesarios)
COPY . .

RUN mkdir -p storage/framework/views && mkdir -p storage/framework/cache && mkdir -p storage/framework/sessions && mkdir -p storage/framework/testing
### Instalar las dependencias de Composer
RUN composer install --no-dev --prefer-dist --no-interaction

### Copiar la configuración de Nginx
COPY nginx/default.conf /etc/nginx/sites-available/default

### Exponer los puertos 80 y 443 para Nginx
EXPOSE 80 443

### Iniciar Nginx y PHP-FPM
CMD service nginx start && php-fpm

## Archivos configurácion kubernetes (k8s)
Los archivos de configuración de kubernetes se encuentra en la carpeta k8s. Son tres:
1. deployment.yml
    apiVersion: apps/v1
    kind: Deployment
    metadata:
      name: pokedex
    spec:
      replicas: 1
      selector:
        matchLabels:
          app: pokedex
      template:
        metadata:
          labels:
            app: pokedex
        spec:
          containers:
            - name: pokedex
              image: ziki142/pokedex:latest
              ports:
                - containerPort: 80
              env:
                - name: DB_HOST
                  value: mysql-service
                - name: DB_DATABASE
                  value: pokedex
                - name: DB_USERNAME
                  value: root
                - name: DB_PASSWORD
                  value: root
              volumeMounts:
                - name: storage
                  mountPath: /var/www/html/storage
          volumes:
            - name: storage
              emptyDir: {}

   EXPLICACIÓN
   
2. mysql-deployment.yml
    apiVersion: v1
    kind: PersistentVolumeClaim
    metadata:
      name: mysql-pvc
    spec:
      accessModes:
        - ReadWriteOnce
      resources:
        requests:
          storage: 1Gi
    
    ---
    apiVersion: apps/v1
    kind: Deployment
    metadata:
      name: mysql
    spec:
      replicas: 1
      selector:
        matchLabels:
          app: mysql
      template:
        metadata:
          labels:
            app: mysql
        spec:
          containers:
            - name: mysql
              image: mysql:8.0
              ports:
                - containerPort: 3306
              env:
                - name: MYSQL_ROOT_PASSWORD
                  value: root
                - name: MYSQL_DATABASE
                  value: pokedex
              volumeMounts:
                - name: mysql-storage
                  mountPath: /var/lib/mysql
          volumes:
            - name: mysql-storage
              persistentVolumeClaim:
                claimName: mysql-pvc

EXPLICACION
   
3. service.yml
    apiVersion: v1
    kind: Service
    metadata:
      name: pokedex-service
    spec:
      selector:
        app: pokedex
      ports:
        - protocol: TCP
          port: 80
          targetPort: 80
      type: NodePort
    
    ---
    apiVersion: v1
    kind: Service
    metadata:
      name: mysql-service
    spec:
      selector:
        app: mysql
      ports:
        - protocol: TCP
          port: 3306
      clusterIP: None
EXPLICACIÓN
