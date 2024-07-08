# SIMONEV v21
SIMONEV sesuai dengan permendagri no 98 tahun 2020

# Build
## instalasi
abaikan jenis mesin
```
yarn install --ignore-engines
```
## Node versi 20.12.2
Saat build di node versi ini, terkendala dengan versi openssl baru. Solusinya adalah menggunakan openssl versi lama.

di Unix-like systems (Linux, macOS, Git Bash, etc.):
```
export NODE_OPTIONS=--openssl-legacy-provider
```
di Windows CLI:
```
set NODE_OPTIONS=--openssl-legacy-provider
```
di Powershell:
```
$env:NODE_OPTIONS = "--openssl-legacy-provider"
```

Edit di package.json, sehingga menjadi seperti berikut:
```
"serve": "vue-cli-service serve --host localhost --openssl-legacy-provider",
"build": "vue-cli-service build --openssl-legacy-provider",
```