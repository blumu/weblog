To deploy the site to Azure you need to make sure to properly set 
the path to PHP-cgi.exe in web.config
   
   <handlers>
     <add name="PHP via FastCGI" 
           path="*.html" 
           verb="*" 
           modules="FastCgiModule" 
           scriptProcessor="D:\Program Files (x86)\PHP\v5.4\php-cgi.exe" 
           resourceType="Unspecified" /> 
   </handlers> 
   
as well as in web.roleconfig:

  <system.webServer>
    <fastCgi>
      <application fullPath="D:\Program Files (x86)\PHP\v5.4\php-cgi.exe" />
    </fastCgi>
  </system.webServer>