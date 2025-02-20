# POKEDEX LARAVEL


## Explicacion app

Nuestra aplicación es una aplicación web que se sirve de la API de [PokeAPI](https://pokeapi.co/) para crear una página web que sea como una pokédex.

En la página web puedes registrate y crear un usuario que se guarda en una base de datos para que cuando hagas Login poder acceder a todas las funcionalidades de la página.

Las funcionalidades son:
- Registro
![Registro](https://github.com/user-attachments/assets/b036ed59-a32f-4364-ba28-7ebbf96c333d)

- Login con autenticación
![Login](https://github.com/user-attachments/assets/26b4805b-d949-4b3b-ad0c-a63b1751fcdb)

- Consultas a la API externa

En **pódedex laravel** podemos buscar hasta 1025 pokemons ya sea por su nombre o por su nº de pokedex, al acceder a la pokedex siempre cargará un pokemon aleatorio, si buscamos pokemons sin nombre o nº de pokedex saldrá un pokemon aleatorio. 
![Pokemon aleatorio 1](https://github.com/user-attachments/assets/af7946b6-5a37-4917-8d23-8ce260d11b48)

Al buscar dicho pokemon nos saldrá características del pokemon buscado, como:
- Foto
- Nombre
- Tipo
- Estadísticas de combate

![Pikachu](https://github.com/user-attachments/assets/fdec058a-a39d-4ba7-9033-890e6de1efb3)


## Tecnologias utilizadas
1.  **Laravel**\
    Es un framework de PHP que facilita el desarrollo de aplicaciones web con una estructura limpia y organizada. Proporciona herramientas como enrutamiento, autenticación, ORM (Eloquent) y migraciones para bases de datos.

2.  **Breeze (Alpine.js y Blade) para vistas, seguridad y dinamismo**

    -   **Breeze**: Un starter kit liviano de Laravel que incluye autenticación básica.
    -   **Alpine.js**: Una librería JavaScript minimalista que añade interactividad a las vistas sin necesidad de un framework pesado.
    -   **Blade**: El motor de plantillas de Laravel que permite escribir vistas reutilizables con una sintaxis simple y eficiente.
  
3.  **Axios.js para peticiones a API externa**\
    Una librería de JavaScript para realizar solicitudes HTTP de manera sencilla y eficiente. Se usa comúnmente en aplicaciones web para interactuar con APIs externas.

4.  **Nginx para el servidor web**\
    Un servidor web de alto rendimiento que se usa para servir la aplicación Laravel, actuar como proxy inverso y manejar el tráfico de manera eficiente.

5.  **Docker para la imagen de la app**\
    Permite empaquetar la aplicación Laravel en un contenedor junto con sus dependencias, asegurando que funcione de manera consistente en cualquier entorno.

6.  **Kubernetes con Minikube para el despliegue local**

    -   **Kubernetes**: Un sistema de orquestación de contenedores que gestiona la implementación, escalabilidad y disponibilidad de aplicaciones en contenedores.
    -   **Minikube**: Una herramienta que permite ejecutar un clúster de Kubernetes localmente, ideal para desarrollo y pruebas antes de llevar la aplicación a producción.


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

###  **Definición de la imagen base**


`FROM php:8.2-fpm AS base`

-   Se usa **PHP 8.2 con FPM (FastCGI Process Manager)** como base.
-   Se nombra esta etapa como `base`, lo que permite reutilizarla en builds multi-etapa si fuera necesario.


###  **Instalación de paquetes del sistema y extensiones de PHP**

```
RUN apt-get update && apt-get install -y\
    nginx\
    libpng-dev\
    libjpeg62-turbo-dev\
    libfreetype6-dev\
    libzip-dev\
    git\
    unzip\
    curl\
    && docker-php-ext-configure gd --with-freetype --with-jpeg\
    && docker-php-ext-install gd zip pdo pdo_mysql\
    && rm -rf /var/lib/apt/lists/*
```

-   **Actualiza los repositorios** con `apt-get update`.
-   **Instala paquetes necesarios**:
    -   `nginx`: Servidor web.
    -   `libpng-dev`, `libjpeg62-turbo-dev`, `libfreetype6-dev`: Librerías para trabajar con imágenes.
    -   `libzip-dev`: Soporte para trabajar con archivos comprimidos en PHP.
    -   `git`, `unzip`, `curl`: Herramientas útiles para descargas y gestión de código.
-   **Configura e instala extensiones de PHP**:
    -   `gd`: Librería para manipular imágenes, con soporte para FreeType y JPEG.
    -   `zip`: Soporte para archivos `.zip`.
    -   `pdo` y `pdo_mysql`: Para conexión con bases de datos MySQL.
-   **Limpia la caché de `apt`** para reducir el tamaño de la imagen.



###  **Instalación de Composer**



`RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer`

-   Descarga e instala **Composer** (gestor de dependencias de PHP) en `/usr/local/bin`.


###  **Configuración del entorno de trabajo**



`WORKDIR /var/www/html`

-   Define `/var/www/html` como el **directorio de trabajo** dentro del contenedor.



###  **Copia del código fuente**



`COPY . .`

-   Copia **todo el código del proyecto** desde la máquina host al contenedor.
  

###  **Creación de directorios para almacenamiento en Laravel**


`RUN mkdir -p storage/framework/views && mkdir -p storage/framework/cache && mkdir -p storage/framework/sessions && mkdir -p storage/framework/testing`

-   Crea los directorios dentro de `storage/framework/` necesarios para Laravel:
    -   `views`: Caché de vistas compiladas.
    -   `cache`: Caché de la aplicación.
    -   `sessions`: Almacenamiento de sesiones.
    -   `testing`: Caché para pruebas.


### **Instalación de dependencias de PHP (excluyendo las de desarrollo)**



`RUN composer install --no-dev --prefer-dist --no-interaction`

-   Instala las dependencias de la aplicación **sin incluir las de desarrollo** (`--no-dev`).
-   Usa `--prefer-dist` para descargar paquetes comprimidos en lugar de fuentes (más rápido).
-   Usa `--no-interaction` para evitar preguntas durante la instalación.


###  **Configuración de Nginx**



`COPY nginx/default.conf /etc/nginx/sites-available/default`

-   Copia un archivo de configuración de Nginx desde el código fuente (`nginx/default.conf`) al directorio de configuración de Nginx dentro del contenedor.


###  **Exposición de puertos**



`EXPOSE 80 443`

-   **Expone los puertos 80 y 443** para que el servidor Nginx pueda atender solicitudes HTTP y HTTPS.


###  **Comando de inicio**



`CMD service nginx start && php-fpm`

-   Inicia **Nginx** (`service nginx start`).
-   Luego inicia **PHP-FPM** (`php-fpm`), que manejará las peticiones PHP.


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

#### 1\. `PersistentVolumeClaim` (PVC)

-   **`apiVersion: v1`**: Especifica que este recurso utiliza la versión 1 de la API de Kubernetes.
-   **`kind: PersistentVolumeClaim`**: Define una solicitud para almacenamiento persistente en Kubernetes.
-   **`metadata`**: El nombre del PVC es "mysql-pvc".
-   **`spec`**:
    -   **`accessModes`**: Define cómo se puede acceder al volumen. En este caso, es `ReadWriteOnce`, lo que significa que el volumen puede ser montado por un solo nodo de Kubernetes en modo lectura y escritura.
    -   **`resources.requests`**: Solicita 1 GiB de almacenamiento para la base de datos MySQL.

#### 2\. `Deployment` para MySQL

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

#### 1\. **`Service` para Pokedex**

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

#### 2\. **`Service` para MySQL**

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

## Configuración

 1. Instalar minikube

    Para ello iremos a la página oficial de [minikube](https://minikube.sigs.k8s.io/docs/start/?arch=%2Fwindows%2Fx86-64%2Fstable%2F.exe+download) y descargamos el instalador dependiendo de nuestro SO.
   
3. Instalar kubectl

   Nos iremos a la página oficial de minikube e instalaremos [kubectl](https://kubernetes.io/docs/tasks/tools/install-kubectl-windows/).

5. Iniciar minikube

   Abriremos un cmd e iniciamos minikube con el siguiente comando: `minikube start`
   
4. Verificar el estado de minikube

    Con el comando `minikube status` comprobamos que los componentes de minikube estan funcionando
   
5. Configurar kubectl para usar minikube

    Con el comando `kubectl config use-context minikube` nos aseguraremos que `kubectl` apunta al cluster de minikube

6. Verificar la configuración

   Con el comando `kubectl get pods` podremos ver una lista con todos los nodos del cluster de minikube que esten activos

## Despliegue 

### Clonado del repositorio y crear la imagen
Clonamos el proyecto de github desde un terminal con `git clone https://github.com/ImZiki/Pokedex-laravel.git` y nos vamos a la carpeta raiz del proyecto con `cd pokedex-laravel`
Una vez en el directorio, usaremos `docker build -t ziki142/pokedex:latest .` para crear la imagen docker del proyecto, que utilizaremos luego en el despliegue.

### Despliegue en el cluster de Minikube
Estando en la carpeta raiz del proyecto, con minikube corriendo haremos uso de `kubectl apply -f k8s/` para aplicar los deployments y services que tenemos configurados en los archivos `deployment.yml`, `mysql-deployment.yml` y `service.yml` que contienen toda la informacion de la aplicación y las directrices que ya expusimos antes para que funcione todo correctamente.

Una vez termine el proceso, haremos uso de los comandos `kubectl get pods` y `kubectl get svc` para asegurarnos de que todo se ha creado correctamente.

Luego, entraremos en el nodo de pokedex con el comando `kubectl exec -it nombredelnodo -- bash` y escribiremos los comandos `php artisan migrate:fresh` y `php artisan db:seed` para que se creen todas las tablas de la base de datos y se rellenen con los datos que nos fueran necesarios. En nuestro caso, el seed rellena la tabla `pokemon_types` con los codigos de color de los tipos pokemon. Cuando se completen, podremos salir escribiendo `exit` en la terminal.

Para poder servir nuestra aplicación y poder usarla de manera local con minikube, usamos el comando `minikube service nombredelservicio --url` y este nos dará una URL parecida a `127.0.0.1:puerto` el puerto será asignado por minikube de manera aleatoria.

Una vez hecho todo esto, para cuando queramos parar la aplicación, solo tendremos que hacer uso de las teclas `Ctrl + C` y el comando `minikube stop` y el cluster se parará.
