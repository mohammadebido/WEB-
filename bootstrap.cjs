const cp=require('child_process');
function run(cmd,args){const r=cp.spawnSync(cmd,args,{stdio:'inherit',env:process.env});if(r.status!==0)process.exit(r.status||1);}
run('unzip',['-o','bundle.zip','-d',process.cwd()]);
run('node',['build.cjs']);
