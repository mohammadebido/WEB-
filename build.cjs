const fs=require('fs');
const cp=require('child_process');
const path=require('path');
const zlib=require('zlib');
function run(cmd,args){const r=cp.spawnSync(cmd,args,{stdio:'inherit',env:process.env});if(r.status!==0)process.exit(r.status||1);}
let b64='';
for(let i=0;i<4;i++) b64+=fs.readFileSync(path.join(process.cwd(),`source${i}.b64`),'utf8').trim();
const base=zlib.brotliDecompressSync(Buffer.from(b64,'base64'));
const baseTar=path.join('/tmp','tajerflow-v19-base.tar');
fs.writeFileSync(baseTar,base);
run('tar',['-xf',baseTar,'-C',process.cwd()]);
let overlayB64='';
for(let i=0;i<3;i++) overlayB64+=fs.readFileSync(path.join(process.cwd(),`v110-overlay${i}.b64`),'utf8').trim();
const overlay=zlib.brotliDecompressSync(Buffer.from(overlayB64,'base64'));
const overlayTar=path.join('/tmp','tajerflow-v110-overlay.tar');
fs.writeFileSync(overlayTar,overlay);
run('tar',['-xf',overlayTar,'-C',process.cwd()]);
const nextBin=path.join(process.cwd(),'node_modules','.bin','next');
run(nextBin,['build','--webpack']);
