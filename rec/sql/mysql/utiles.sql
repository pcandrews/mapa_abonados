# Agrega todos los numero abonados faltantes 
# de ccc._temp_app_relevamiento a ccc.abonados
INSERT IGNORE INTO ccc.abonados (numero_abo) 
SELECT DISTINCT numero_abonado
FROM ccc.csv_app_relevamiento 
WHERE numero_abonado NOT IN (SELECT numero_abo 
							 FROM ccc.abonados);                            
###   

# Devuelve la cantidad de numeros abonados distintos que existen en ccc._temp_csv_app_relevamiento
(SELECT COUNT(DISTINCT numero_abonado) FROM ccc._temp_csv_app_relevamiento);  

###

SELECT MAX(id_temp_csv) 
FROM ccc._temp_csv_app_relevamiento;
###


#Inverso a distinct
SELECT * 
FROM ccc.rel_abonados_instalaciones_servicios
WHERE numero_abo IN (SELECT numero_abo 
					 FROM ccc.rel_abonados_instalaciones_servicios 
					 GROUP BY numero_abo 
					 HAVING COUNT(*) > 1)
ORDER BY numero_abo;
###

SHOW TABLES;
###


SELECT * 
FROM instalaciones_servicios
WHERE id_inst_serv NOT IN (SELECT id_inst_serv FROM rel_instalaciones_servicios_celulares);
###

#probar fotos
INSERT IGNORE INTO ccc.fotos_instalaciones (numero_abonado,
											rut_arch_orig_foto_inst, 
											rut_arch_bu_foto_inst) 
VALUES (509217,
	  '/home/pablo/Proyectos/Web/PFW/mapa_abonados/back_end/uhfapp//jpg/clie_509217.jpg',
	  '/home/pablo/Proyectos/Web/PFW/mapa_abonados/back_end/data/backup_uhfapp/jpg/clie_509217.jpg');
###

SELECT COUNT(*) FROM ccc.crudo_uhfapp WHERE lat_c_uhfapp = 0 OR lng_c_uhfapp = 0


SELECT COUNT(*) FROM ccc.crudo_uhfapp WHERE lat_c_uhfapp = 0 OR lng_c_uhfapp = 0






SELECT count(*) AS Total_duplicate_count
FROM
(SELECT numero_abonado FROM abonados
GROUP BY numero_abonado HAVING COUNT(numero_abonado) > 1
)AS numero_abonado




SELECT * FROM ccc.crudo_uhfapp_temp
WHERE id_c_uhfapp_t NOT IN (SELECT id_c_uhfapp FROM ccc.crudo_uhfapp)

UNION

SELECT id_coord_gps FROM ccc.coordenadas_gps
WHERE  lat_c_gps AND lng_c_gps NOT IN (SELECT lat_c_uhfapp, lng_c_uhf FROM ccc.crudo_uhfapp);



SELECT COUNT(DISTINCT n_abonado_c_uhfapp) from crudo_uhfapp
