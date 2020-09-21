@echo off
cls
pushd %~dp0

.paket\paket.bootstrapper.exe
if errorlevel 1 (
  exit /b %errorlevel%
)

.paket\paket.exe restore
if errorlevel 1 (
  exit /b %errorlevel%
)

msbuild.exe -p:Configuration=Debug

packages\FAKE\tools\FAKE.exe build.fsx %*

popd