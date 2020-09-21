git clone https://luweiblog.scm.azurewebsites.net:443/luweiblog.git wwwroot
packages\FAKE\tools\FAKE.exe build.fsx Stage
pushd wwwroot
git commit
git push
popd