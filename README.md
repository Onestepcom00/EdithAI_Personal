[![EdithAI Personal : Agent IA  capable d'effectuer des taches ](./public_html/assets/image/logo-edithai.jpg)]

# EdithAI Personal : Agent IA capable d'effectuer des taches

**EdithAI Personal** est un agent d'intelligence artificielle conçu pour répondre aux questions et effectuer des tâches selon les outils disponibles. Ce projet met l'accent sur la flexibilité et l'extensibilité, permettant aux développeurs d'ajouter facilement leurs propres outils pour répondre à des besoins spécifiques.

---

## Explication detaillée

Le projet **EdithAI Personal** a été créer a la base pour ses developpeurs qui veulent d'une fonctionnalité mais malheuresement la fonctionnalitées qu'ils veulent n'est pas directement disponible depuis **ChatGPT** ou autres outils IA , alors avec EdithAI Personal va leurs permettre de deveopper ou telecharger l'outil dont ils ont besoin puis passer cette outil a l'IA et l'IA va correctement l'utiliser , donc vous n'etes pas obliger de modifier completement notre code de base , créer juste l'outil dont vous avez besoin  et laissez EdithAI faire.

---


## Fonctionnalités principales

- **Réponses intelligentes** : EdithAI Personal comprend et répond aux questions des utilisateurs.
- **Exécution de tâches** : L'agent peut effectuer diverses tâches selon les outils intégrés.
- **Extensible** : Les développeurs peuvent créer et intégrer leurs propres outils, que l'IA utilisera automatiquement selon la demande.
- **Architecture Tool Management (Librix)** : Gestion centralisée et dynamique des outils côté backend.

---

## Architecture technique

- **Backend** : PHP avec l'architecture Tool Management (Librix)
- **Frontend** : HTML, TailwindCSS, JavaScript


---

## Installation

### 1. Installer WampServer ou un environnement similaire

#### Sur Windows

- Téléchargez WampServer depuis [wampserver.com](https://www.wampserver.com/).
- Suivez l'assistant d'installation et lancez WampServer.

#### Sur Linux

- Utilisez [XAMPP](https://www.apachefriends.org/fr/index.html) ou [LAMP](https://doc.ubuntu-fr.org/lamp).
- Installez Apache, MySQL/MariaDB et PHP via votre gestionnaire de paquets.

#### Sur Mac

- Utilisez [MAMP](https://www.mamp.info/en/) ou [XAMPP](https://www.apachefriends.org/index.html).
- Installez et lancez l'environnement.

### 2. Cloner le dépôt

```bash
git clone https://github.com/Onestepcom00/EdithAI_Personal
```



### 3. Configurer le serveur web

- **VirtualHost recommandé** : Lors de la création du projet, les tests ont été réalisés avec un VirtualHost. Il est donc préférable de créer un VirtualHost dans WampServer (ou votre environnement) pointant vers le dossier `ou sera placer le projet dans votre machine` , Dans notre cas nous avons utiliser **edithai.local** comme nom de virtualhost , nous vous le recommandons si vous voulez lancer directement le projet .
- **Activer .htaccess** : Assurez-vous d'autoriser l'utilisation des fichiers `.htaccess` en configurant le VirtualHost (`AllowOverride All` dans la configuration Apache).

### 4. Accéder à l'interface

- Lancez le projet et ouvrez votre navigateur à l'adresse du VirtualHost configuré.

>> Vous devez notez que le projet a été tester uniquement sur **Windows** et uniquement avec **WampServer** , donc en respectant toute les conditions d'installations le projet se lancera sans probleme.


---

## Utilisation : Assurez vous d'avoir bien effectuer les installations 

Une fois les etapes d'installations finis vous devez lancer votre serveur locale executant **PHP** , alors en ce moment assurez vous que votre serveur autorise bien le fichier **.htaccess** et que votre serveur accepte bien le **VirtualHost** , une fois cela est bon , vous allez devoir etre sur que vos APIs LLM sont fonctionnelle , dns mon cas j'avais utiliser mes propres APIs Heberger sur Cloudflare workers , les explications techniques seront mis en bas .

Alors comme tout est bien la , vous allez maintenant ouvrir votre navigateur puis lancer le VirtulHost , juste en tapant le nom du virtualhost dans la barre d'adresse de votre navigateur dans mon cas c'etait : **http://edithai.local** .

Si cela est fait , vous allez par la suite cliquer sur le button **New Chat** car lors de la creation du projet nous avons carrement oublier de lancer le Chat au debut donc vous etes au obliger a chaque fois de cliquer sur **New Chat** pour lancer une nouvelle conversation avec l'IA .

---

## Details Techniques : Vraiment important de le lire avant tout ! 

Il peut arriver que le projet pas fonctionner malgrer avoir suivis toutes les etapes necessaires d'installation et d'utilisation , alors vous devez ouvrir , faites efforts d'ouvrir tout les fichiers et modifier les appels vers les APIs mettez les en fonctions de votre emplacement , pour vous simplifier la tache , ouvrez les fichiers suivant car c'est uniquement dans ces fichiers que vous trouverez des configurations : 

- **public_html/index.php** : Dans ce fichier concentrez vous specialement a la partie JAVASCRIPT car c'est uniquement dans cette partie que vous trouverez les appels APIs
- **public_html/config.php** : Dans ce fichier vous trouverez toutes les configurations necessaires , ajustez le en fonction de vos besoins , ici vous trouverez que des configurations coter Design (Interface) donc par exemple du texte etc...
- **system/api/config.php** : Dans ce fichier vous trouverez toutes les configurations coter backend , donc les liens vers les **APIs LLM** doivent etre changer depuis ce fichier , vous y trouverez aussi quelques configurations lier aux requetes , adaptez le a vos besoins . 

>> Je tiens a preciser que toutes les **APIs** livrez avec ce projet seront couper de service a partir du **20 Septembre 2025** , au cas ou vous aurez besoin du code source de ses APIS alors veuillez nous contactez via notre adresse mail (onestecom00@gmail.com).

Si vous respectez ses fichiers , alors tout marchera bien , et s'il arrive que malgré avoir suivis a la lettre les etapes les erreurs surviennent toujours alors ouvrez le fichier **public_html/index.php** puis retiez la double redirection :

```php


/**
 * 
 * 
 * Verifier si la session existe ou non 
 * 
 * 
 */
if(!isset($_SESSION['data']) || !isset($_SESSION['data']['chat_id'])){
    /**
     * 
     * Si la session n'existe pas , on va rediriger vers la page d'accueil 
     * 
     */
    header('Location:' . APP_URL);
    exit();
}

```

supprimez uniquement cette partie la du code et le projet va fonctionner , ce probleme arrive trop souvent dans le cas ou votre projet est heberger sur Hebergeur comme **Hostinger** par exemple .

---

## Explication des dossiers et fichiers :

En fonction de la configuration que vous allez vouloir faire vous pouvez carrement placer le dossier **system** dans les dossiers **public_html** , alors dans ce cas vous allez modifier uniquement les appels d'APIs dans les fichiers **public_html/index.php** et **public_html/config.php** , vous ne serez pas obliger de modifier la partie **system** car de ce coter la tout sera bon sauf le management tools , alors voici l'explication : 

Coter Frontend : **public_html/**

- **public_html/loader.php** : stocke toutes les fonctions (composants) reutilisables facilement .

Coter Backend : **system/**

Coter backend nous avons precisement 3 dossiers importants , dans chaque dossier nous avons deux fichiers importants **config.php** qui va contenir toute les configurations lier a ce dossier et **loader.php** qui va contenir toutes les fonctions .

- **api/** : Contient toutes les routes APIs necessaires , Contient la gestions du Chat et le sauvegarde.
- **mcp/** :  Il s'agit precisment du tools management , c'est cette partie qui se charge de preparer les outils importants et de le fournir a l'IA selon la demande de l'utilisateur.
- **tools/** : C'est dans ce dossier que vous allez mettre vos outils .


--- 
## Conclusion 

Le projet est tres simple mais trop vaste , nous ne pouvons pas expliquer chaque partie alors , a vous d'explorer , de faire voz experiences personnelles avec ce projet , modifier le , ameliorer le faites en bonne usage , surtout informez moi sur vos udpates.

>> Si vous voulez travailler avec moi en collaboration veuillez me laissez un mail , invitez moi dans vos projets fun et commerciale .

## Author : Un Tag Speciale 

Voici un tag special au developpeur qui a ete derriere ce projet , lui seul depuis le debut jusqu'a la fin .
Nous parlons de **Exauce Stan Malka** de son vrai nom **Exauce Malumba** est un developpeur fullstack , Pentesteur senior et Ingineer IA , dans **Onestep** travaillant aussi dans **Kreatyva** .
[ ] Contact : exaustanmalka@icloud.com / +243 977482151 

## Libix & Creation des outils 

concernant la creation des outils , nous allons creer un repo speciale pour detailler et expliquer clea , dans ce repo nous allons placer plusieurs outils deja disponibles , nous tenons a preciser que le projet n'est pas entierement finit.