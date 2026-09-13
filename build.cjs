const fs=require('fs');
const cp=require('child_process');
const path=require('path');
const zlib=require('zlib');
function run(cmd,args){const r=cp.spawnSync(cmd,args,{stdio:'inherit',env:process.env});if(r.status!==0)process.exit(r.status||1);}
let b64='';
for(let i=0;i<4;i++) b64+=fs.readFileSync(path.join(process.cwd(),`source${i}.b64`),'utf8').trim();
const br=Buffer.from(b64,'base64');
const tar=zlib.brotliDecompressSync(br);
const tarPath=path.join('/tmp','tajerflow-v19-source.tar');
fs.writeFileSync(tarPath,tar);
run('tar',['-xf',tarPath,'-C',process.cwd()]);
const nextBin=path.join(process.cwd(),'node_modules','.bin','next');
run(nextBin,['build','--webpack']);
