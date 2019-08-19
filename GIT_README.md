# GIT_README

## Resumen

### 1 Crear repo

NOTA: La repo debe existir en github.

git init  && git remote add origin git@github.com:pcandrews/nombre_repo.git

### 2 Pull

git pull git://github.com/pcandrews/nombre_repo.git

o usar el script: git-pull.sh

### 3 Push

git add . &&
git commit -m "descripcion" &&
git push -u origin master

o usar el script: git-push-origin-master.sh

###########################################

## Crear un nuevo repo desde cero

    1. Crear el directorio del proyecto.
    2. Ir al directorio del proyecto.
    3. $ git init 
    4. Escribir código.
    5. $ git commit -m "descripción de los cambios" 
    6. El primer archivo que se creará sera probablemente el README.md.

## Una nueva repo desde otra existente

Situación: Existe un proyecto (local) y se desea sincronizar con git.

    1. Ir al directorio del proyecto.
    2. $ git init .
       El punto es para señalar el directorio actual.
    3. $ git add 
       para añadir todos los documentos relevantes.
    4. Probablemente será útil crear un archivo .gitignore para indicar que archivos no se desean sincronizar con git.
    5. $ git add .gitignore
    6. $ git commit -m "descripción de los cambios" 

## Conectar con Github

Hasta aquí la configuración local del git esta completa. Si se desea trabajar de forma local no hay que hacer nada mas, pero si se desea sincronizar con github hay que conectarlo.

    1. Ir a github.
    2. Login en la cuenta.
    3. Clic en el botón ‘new repository’ arriba a la derecha. Se podrá inicializar el repositorio con un archivo README.
    4. Clic ‘Create repository’.
    5. $ git remote add origin git@github.com:pcandrews/nombre_repo.git 
       tener en cuenta en que proyecto se trabaja, muy importante “origin” es el nombre de la maquina local.
    6. $ git pull git://github.com/pcandrews/nombre_repo.git
    7. $ git push -u origin master

## Para hacer un push (subir cambios)

    1. $ git add . && #añade los cambios del directorio actual
    2. $ git commit -m "descripción” &&
    3. $ git push -u origin master #origin es el nombre de la computadora actual y master es la rama donde se suben los cambios

## Para hacer un pull (traer ultima versión)

    1. $ git pull git://github.com/pcandrews/mapa_abonados.git

## Otros Comandos

    1. $ git log  
    2. $ git clone https://github.com/pcandrews/mapa_abonados

## Usar en otra maquina

    1. Ir a la carpeta destina al proyecto.
    2. Clonar el proyecto, no descarlargo como zip, CLONAR el proyecto, ej:
        ◦ git clone git@github.com:pcandrews/login.git
    3. Listo!
