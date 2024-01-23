# dw_4vGYMAPP
<em>:weight_lifting: :muscle: 4vGYM API REST :barbell:  :muscle:</em>


Esta API REST está diseñada para gestionar las actividades, monitores y tipos de actividad de 4vGYM, proporcionando operaciones CRUD (Crear, Leer, Actualizar, Eliminar) y asegurando la integridad de los datos.


/activity-types


GET


Code 404 error->No hay actividades
Code 500 error->No hay conexion

/monitors


GET 



CODE 404 error->NO hay monitores
CODE 500 error-No hay conexion
POST



CODE 400- ERROR->Estan mal los datos
CODE 500 error-No hay conexion

PUT


/monitors/{id}



CODE 404 -Error->No encontrado el monitor


CODE 400- ERROR->Estan mal los datos


CODE 200 -ZMonitro cambiado



DELETE



/monitors/{id}




CODE 404 -Error->No encontrado el monitor



CODE 200 -ZMonitro cambiado



CODE 500 error-No hay conexion



/activities



GET



Exception FECHA PASADA no valida


CODE 400 No hay conexion o diferente


CODE 404 No hay ACTIVIDADES DE ESA FECHA


CODE 500 error-No hay conexion



POST



CODE 404 no se ha encontrado es tipo de actividad


No se ha encontrado ese monitor


CODE 406 no hay suficnetes monitores para esa tipo de actividad



/activities/{id}
PUT


CODE 404 Actividad no encotrda


ActivitType no enconotrada


CODE 400 Los dato sno son correctos


CODE 500 error-No hay conexion

DELETE



CODE 404 Actividad no encotrda


CODE 200 aCTIVIDAD ELIMINADA


CODE 500 error-No hay conexion



Monitores:
ID (clave primaria)
Nombre
Email
Teléfono
Foto
Tipos de Actividad:

ID (clave primaria)
Nombre
Número de Monitores Necesarios
Actividades:

ID (clave primaria)
Tipo de Actividad (clave foránea a Tipos de Actividad)
Fecha
Duración
Otros detalles de la actividad
Actividades-Monitores (N-M):

ID (clave primaria)
Actividad (clave foránea a Actividades)
Monitor (clave foránea a Monitores)
Validación de Campos
Se realiza validación de campos en las operaciones POST para garantizar la integridad de los datos.

¡Disfruta utilizando la API 4vGYM para gestionar las actividades y monitores de tu gimnasio!
