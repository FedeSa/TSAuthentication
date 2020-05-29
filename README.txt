Il meccanismo per essere testato ha bisogno che sia installato
 - XAMPP (la mia versione e' v3.2.4)

Bisogna che Apache e MySQL vengano lanciati (cliccare "Start")

Si deve inserire la cartella all'interno di htdocs:
 - dal Control Panel di XAMPP
 - cliccare su "Explorer" sulla parte destra
 - verra' aperta una cartella 
 - cercare la cartella "htdocs" ed entrarci
 - copiare la cartella contentente i codici "TSAuth"

Inoltre deve essere creato un database su phpMyAdmin:
- dal Control Panel di XAMPP, nella riga di MySQL, cliccare ADMIN
- si viene ridiretti nel sito apposito
- sulla sinistra sono esposti i diversi database, cliccare su nuovo
- nominarlo tsauth (tutto minuscolo)
- cliccare su "crea" (bottone sulla destra, sulla stessa riga)
- si viene ridiretti nel database e bisogna inizializzarlo:
 	- in alto cliccare su "Importa"  
	- cliccare su Scegli file
	- cercare la cartella "TSAuth" nel proprio sistema (solitamente C:\xampp\htdocs)
	- aprire il file "tsauth" (estensione SQL)
	- scrollare fino al fondo della pagina e cliccare su "Esegui"

Ora il database e' creato e si puo' procedere con l'inizio del test. 