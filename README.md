# POKEDEX LARAVEL
## Dockerfile
```
FROM php:8.2-fpm AS base

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

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY . .

RUN mkdir -p storage/framework/views && mkdir -p storage/framework/cache && mkdir -p storage/framework/sessions && mkdir -p storage/framework/testing

RUN composer install --no-dev --prefer-dist --no-interaction

COPY nginx/default.conf /etc/nginx/sites-available/default

EXPOSE 80 443

CMD service nginx start && php-fpm
```
EXPLICACIÓN

- **Usar PHP-FPM con Nginx como base**: Se establece la imagen base de PHP-FPM y Nginx, que proporcionará el entorno necesario para ejecutar la aplicación Laravel con PHP y servirla usando Nginx.
  
- **Instalar dependencias del sistema necesarias para Laravel y Nginx**: Se instalan las bibliotecas y herramientas del sistema requeridas para que Laravel funcione correctamente, como las extensiones de PHP y Nginx.

- **Instalar Composer**: Se descarga e instala **Composer**, el administrador de dependencias de PHP, para manejar las bibliotecas de Laravel.

- **Establecer el directorio de trabajo**: Define el directorio en el contenedor donde se copiarán los archivos del proyecto y donde se ejecutarán los comandos dentro del contenedor.

- **Copiar los archivos del proyecto (excluyendo los no necesarios)**: Copia los archivos del proyecto desde el directorio local al contenedor, excluyendo archivos no necesarios mediante un archivo `.dockerignore`.

- **Instalar las dependencias de Composer**: Ejecuta Composer para instalar las dependencias de PHP necesarias para la aplicación Laravel.

- **Copiar la configuración de Nginx**: Copia el archivo de configuración personalizado de Nginx al contenedor para que Nginx se configure según las necesidades del proyecto.

- **Exponer los puertos 80 y 443 para Nginx**: Exponen los puertos **80** y **443** en el contenedor para que Nginx pueda manejar tráfico HTTP y HTTPS desde fuera del contenedor.

- **Iniciar Nginx y PHP-FPM**: Inicia los servicios de Nginx y PHP-FPM para que el contenedor esté listo para servir la aplicación Laravel.


## Archivos configurácion kubernetes (k8s)
Los archivos de configuración de kubernetes se encuentra en la carpeta k8s. Son tres:
1. deployment.yml
    ```
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
    ```
   EXPLICACIÓN

   -   `apiVersion: apps/v1`: Especifica la versión de la API que se usa para interactuar con Kubernetes.-   `kind: Deployment`: Define el tipo de recurso a crear, en este caso un *Deployment*, que se encarga de gestionar los pods y las réplicas de la aplicación.-   `metadata`: Contiene metadatos sobre el recurso, en este caso el nombre del deployment es "pokedex".-   `spec`: Define la configuración deseada para el deployment:
    -   `replicas: 1`: Se quiere una sola réplica (pod) de la aplicación corriendo.
    -   `selector`: Se utiliza para identificar los pods gestionados por este deployment. Los pods se etiquetan con `app: pokedex`.
    -   `template`: Define la plantilla de los pods que se crearán:
        -   `metadata`: Las etiquetas del pod, nuevamente con `app: pokedex`.
        -   `spec`: Especifica los contenedores que debe ejecutar el pod:
            -   Un contenedor llamado "pokedex" que usa la imagen Docker `ziki142/pokedex:latest` y expone el puerto 80.
            -   `env`: Define variables de entorno para conectar la aplicación a una base de datos, especificando los parámetros `DB_HOST`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`.
            -   `volumeMounts`: Monta un volumen llamado "storage" en la ruta `/var/www/html/storage` dentro del contenedor.
    -   `volumes`: Define un volumen llamado "storage" de tipo `emptyDir`, que crea un directorio temporal en el pod.
   
3. mysql-deployment.yml
    ```
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
     ```
EXPLICACIÓN

### 1\. `PersistentVolumeClaim` (PVC)

-   **`apiVersion: v1`**: Especifica que este recurso utiliza la versión 1 de la API de Kubernetes.
-   **`kind: PersistentVolumeClaim`**: Define una solicitud para almacenamiento persistente en Kubernetes.
-   **`metadata`**: El nombre del PVC es "mysql-pvc".
-   **`spec`**:
    -   **`accessModes`**: Define cómo se puede acceder al volumen. En este caso, es `ReadWriteOnce`, lo que significa que el volumen puede ser montado por un solo nodo de Kubernetes en modo lectura y escritura.
    -   **`resources.requests`**: Solicita 1 GiB de almacenamiento para la base de datos MySQL.

### 2\. `Deployment` para MySQL

-   **`apiVersion: apps/v1`**: Usa la versión 1 de la API para *Deployments*.
-   **`kind: Deployment`**: Define el *Deployment* de MySQL.
-   **`metadata`**: El nombre del *Deployment* es "mysql".
-   **`spec`**:
    -   **`replicas: 1`**: Solo se crea una réplica (un pod) para MySQL.
    -   **`selector`**: Define el selector de etiquetas para identificar los pods gestionados por este *Deployment* (en este caso, con la etiqueta `app: mysql`).
    -   **`template`**:
        -   **`metadata`**: Las etiquetas del pod también son `app: mysql`.
        -   **`spec`**:
            -   **`containers`**: Define un contenedor llamado "mysql" que usa la imagen `mysql:8.0`.
            -   **`ports`**: El contenedor expone el puerto 3306, que es el puerto estándar de MySQL.
            -   **`env`**: Define las variables de entorno para configurar MySQL:
                -   `MYSQL_ROOT_PASSWORD`: Contraseña del usuario root de MySQL (en este caso, `root`).
                -   `MYSQL_DATABASE`: El nombre de la base de datos inicial que se crea al iniciar el contenedor (en este caso, `pokedex`).
            -   **`volumeMounts`**: Monta el volumen "mysql-storage" en la ruta `/var/lib/mysql`, donde MySQL almacena sus datos.
    -   **`volumes`**: Define un volumen llamado "mysql-storage" que está respaldado por el PVC llamado "mysql-pvc", asegurando que los datos de MySQL sean persistentes entre reinicios del pod.
   
3. service.yml
    ```
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
    
    ```
    
EXPLICACIÓN

### 1\. **`Service` para Pokedex**

-   **`apiVersion: v1`**: Utiliza la versión 1 de la API para el servicio.
-   **`kind: Service`**: Define un servicio en Kubernetes.
-   **`metadata`**: El nombre del servicio es `pokedex-service`.
-   **`spec`**:
    -   **`selector`**: Define un selector de etiquetas para dirigir el tráfico al pod correspondiente, en este caso, los pods con la etiqueta `app: pokedex`.
    -   **`ports`**: Configura el puerto en el cual el servicio estará disponible:
        -   **`port: 80`**: El servicio escucha en el puerto 80.
        -   **`targetPort: 80`**: El tráfico se dirige al puerto 80 dentro de los contenedores seleccionados (en este caso, los contenedores de la aplicación *pokedex*).
        -   **`protocol: TCP`**: El servicio utiliza el protocolo TCP.
    -   **`type: NodePort`**: Define que el servicio será accesible externamente a través de un puerto en los nodos de Kubernetes (puerto dinámico asignado dentro de un rango específico, generalmente entre 30000-32767).

### 2\. **`Service` para MySQL**

-   **`apiVersion: v1`**: Utiliza la versión 1 de la API para el servicio.
-   **`kind: Service`**: Define un servicio para MySQL.
-   **`metadata`**: El nombre del servicio es `mysql-service`.
-   **`spec`**:
    -   **`selector`**: El selector de etiquetas asegura que este servicio se dirija al pod de MySQL (etiquetado con `app: mysql`).
    -   **`ports`**: Configura el puerto en el cual el servicio estará disponible:
        -   **`port: 3306`**: El servicio escucha en el puerto 3306, que es el puerto estándar para MySQL.
        -   **`protocol: TCP`**: El servicio usa el protocolo TCP.
    -   **`clusterIP: None`**: Esto configura el servicio como un "Headless Service", lo que significa que no se asigna una dirección IP interna para el servicio, pero aún así los pods pueden comunicarse entre sí directamente (ideal para bases de datos o cuando se necesitan direcciones específicas para los pods).

## Nginx.conf

```
server {
    listen 80;
    server_name localhost;
    root /var/www/html/public;

    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.ht {
        deny all;
    }
}

```

-   **`server {}`**: Bloque que define un servidor en Nginx.

    -   **`listen 80;`**: El servidor escucha en el puerto 80 (HTTP).

    -   **`server_name localhost;`**: El nombre del servidor es `localhost`, indicando que responderá a solicitudes a este nombre.

    -   **`root /var/www/html/public;`**: Define el directorio raíz del sitio web, que es `/var/www/html/public`.

    -   **`index index.php index.html index.htm;`**: Especifica los archivos que Nginx buscará primero cuando se acceda a un directorio (en orden: `index.php`, `index.html`, `index.htm`).

    -   **`location / {}`**: Configura cómo manejar las solicitudes a la raíz del sitio:

    -   **`try_files $uri $uri/ /index.php?$query_string;`**: Intenta servir el archivo solicitado, si no se encuentra, redirige a `index.php` pasando los parámetros de la consulta.-   **`location ~ \.php$ {}`**: Configura cómo manejar las solicitudes PHP:

    -   **`include fastcgi_params;`**: Incluye los parámetros necesarios para FastCGI.
    -   **`fastcgi_pass unix:/run/php/php-fpm.sock;`**: Pasa las solicitudes PHP al servicio PHP-FPM mediante un socket UNIX.
    -   **`fastcgi_index index.php;`**: Especifica el archivo `index.php` como archivo predeterminado en directorios.
    -   **`fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;`**: Define la ruta completa al archivo PHP solicitado.-   **`location ~ /\.ht {}`**: Bloquea el acceso a los archivos `.ht` (como `.htaccess`) por razones de seguridad:

    -   **`deny all;`**: Deniega todas las solicitudes a archivos que comiencen con `.ht`.

# Pasos para desplegar la aplicación.
Tendremos que hacer varios pasos para podes desplegar la aplicación en kubernetes, para ello debemos seguir lo siguiente:

1. Instalar minikube
   
   Para ello iremos a la página oficial de [minikube](https://minikube.sigs.k8s.io/docs/start/?arch=%2Fwindows%2Fx86-64%2Fstable%2F.exe+download) y descargamos el instalador dependiendo de nuestro SO.
