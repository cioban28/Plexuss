# Environment Setup(Mac)

#### Php 7.0
**1. Install Xampp**
\
    Download the latest version of XAMPP using the following url https://www.apachefriends.org/download.html#849

**2. Clone Repo**
\
   Clone the repository in the following PATH , note that you must create a new folder, call it laravel **/Applications/XAMPP/htdocs/laravel/newplexuss**

**3. Upadte Shell profile**
- Open `~/.bash_profile` with you favourite editor
- Paste the following code
    ```
    export XAMPP_HOME=/Applications/XAMPP 
    export PATH=${XAMPP_HOME}/bin:${PATH} 
    export PATH
    ```
- Save and exit the file
- `source ~/.bash_profile`
- Confirm that you have php installed on your machine via the following command
    ```
    $ which php
    /Applications/XAMPP/bin/php
    ```
    If you dont see above that means you may still have the old php version, make sure you have the xampp php version by running php -v
    
**4. Setup Hosts**
- Open the /etc/hosts file. ( You can do that by right click on finder, and selecting Go to Folder)
- Add the following to the hosts file.
`127.0.0.1      plexuss.local`
- Open
\
**/Applications/XAMPP/xamppfiles/etc/httpd.conf**
\
Make sure the following is uncommented
    ```
    # Virtual hosts
    Include /Applications/XAMPP/etc/extra/httpd-vhosts.conf
    ```
- Open
\
**/Applications/XAMPP/xamppfiles/etc/extra/httpd-vhosts.conf**
\
Paste the following there
    ```
    <Directory /Applications/XAMPP/xamppfiles/htdocs/laravel/>
        Order Deny,Allow   
        Allow from all 
    </Directory>

    <VirtualHost *:80>
        ServerName plexuss.local
        DocumentRoot "/Applications/XAMPP/xamppfiles/htdocs/laravel/newplexuss/public"
    </VirtualHost>
    ```

**5. Install Mycrypt**
- `brew install homebrew/php/php70-mcrypt`
    Make sure mod_rewrite is enabled(look in /etc/httpd.conf)

**6. Install dependencies via Composer**
- Run the following commands to install composer and upadte dependencies
    ```
    $ cd /usr/local/bin         # chnage diretctory
    $ curl -sS https://getcomposer.org/installer | php      # Get Composer
    $ mv composer.phar composer # Rename composer.phar to 
    $ chmod a+x composer        # Make the phar executable
    $ cd /path/to/my/project    # Chnage current directory to the project directory
    $ composer update
    ```
 \   
**7. SetUp Permissions**
- Now let’s make sure storage has the permission as well.
    ```
    sudo su
    php artisan cache:clear
    chmod -R 777 storage 
    php composer.phar dump-autoload
    ```
- Restart all services

**8. Finnal Step**
- Ask Anthony for the env file
\
**AND VOILA, EVERYTHING SHOULD WORK!!**


# Development
##### Staging Server: https://dev.plexuss.com
Any barnch can be deployed to this server for testing

1. The main branch is **DevPlexussWebsite**, so every new feature branch must be craeted from this branch.
2. Webpack is used for bundling  **JS** files. Currently we are bundling ***Admin Dashboard** and **StudentAPP** modules. (Install webpack globally and version less than 1.15)
3. After making changes to the js files, run
    ```
    npm run build_student   # For Student Module
    npm run build_dashboard # For admin dashboard
    ```
    This out put will be like this, updating the 2 files
    ```
    Hash: 47807e169e03988eed13
    Version: webpack 1.15.0
    Time: 12000ms
                     Asset         Size           Chunks   Chunk Names
    ./StudentApp_bundle.js      5.33 MB     0  [emitted]          main
    ./StudentApp_bundle.map     6.51 MB     0  [emitted]          main
    ````
