:: See https://github.com/projectkudu/kudu/wiki/Custom-Deployment-Script
%~dp0\..\.paket\paket.bootstrapper.exe
pushd %~dp0
..\.paket\paket install
packages\FSharp.Compiler.Tools\tools\fsi.exe --define:PUBLISH build.fsx %WEBSITE_HOSTNAME%
popd