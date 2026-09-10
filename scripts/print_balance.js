const fs = require('fs');
const s = fs.readFileSync('c:/xampp/htdocs/Sistem_ig/public/js/script.js','utf8');
const lines = s.split(/\r?\n/);
let bal = 0;
for (let i=0;i<lines.length;i++){
  const line = lines[i];
  let delta = 0;
  for (const ch of line){ if (ch === '{') delta++; else if (ch === '}') delta--; }
  if (delta !== 0) console.log('L'+String(i+1).padStart(4),'delta',delta,'balBefore',bal,'-> balAfter',bal+delta, '\t', line.trim());
  bal += delta;
}
console.log('final balance', bal);
