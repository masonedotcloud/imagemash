# Facemash

Facemash è un'applicazione che consente di confrontare volti tra loro, simile a un sistema di votazione per determinare chi è il più popolare. Questo progetto richiede un database MySQL per memorizzare le informazioni degli utenti e le interazioni.

## Prerequisiti

Assicurati di avere i seguenti strumenti installati sul tuo computer:

- **PHP** (versione 7.x o superiore)
- **MySQL** o **MariaDB**
- Un server web come **Apache** o **Nginx**

## Installazione

1. **Clona il repository**:
   Se non hai già il progetto, clonalo usando Git:
   ```bash
   git clone https://github.com/masonedotcloud/facemash.git
   cd facemash
   ```

2. **Configura il database**:
   Il progetto richiede un database MySQL per funzionare. Per creare il database, usa il file `facemash.sql`, che contiene tutte le tabelle e le strutture necessarie.

3. **Configurazione dell'applicazione PHP**:
   Assicurati che il file di configurazione PHP contenga i dettagli corretti del database:
   - **host**: `localhost`
   - **username**: `tuo_utente`
   - **password**: `tua_password`
   - **database**: `facemash_db`

   Puoi configurare questi parametri nel file `config.php` (se presente) o direttamente nel codice PHP, a seconda della struttura del progetto.

4. **Esegui l'applicazione**:
   - Avvia il server web (Apache, Nginx, ecc.).
   - Visita l'applicazione nel tuo browser all'indirizzo `http://localhost/tuo_progetto`.


## Licenza

Questo progetto è distribuito sotto la Licenza MIT - vedi il file [LICENSE](LICENSE) per ulteriori dettagli.


## Autore

Questo progetto è stato creato da [alessandromasone](https://github.com/alessandromasone).