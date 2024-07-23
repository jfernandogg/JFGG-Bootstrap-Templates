import { defineConfig } from 'vite';

export default defineConfig({
  root: '.', // Define la raíz del proyecto
  build: {
    outDir: 'dist', // Directorio de salida para la construcción
    rollupOptions: {
      input: {
        segbasico: './01-seg-basico.html',
        segenfgraves: './02-seg-enfgraves.html',
        segdesempleo: './03-seg-desempleo.html',
        segincaptemp: './04-seg-incaptemp.html',
        segsenior: './05-seg-senior.html',
        segseniorP: './06-seg-seniorP.html',
        segamascasa: './07-seg-amascasa.html',
        seghospitalizacion: './08-seg-hospitalizacion.html',
        segapreembolso: './09-seg-ap-reembolso.html',
        segapcancer: './10-seg-ap-cancer.html',
        segappremium: './11-seg-ap-premium.html',
        segpfhf: './12-seg-pf-hf.html',
        segpfvf: './13-seg-pf-vf.html',
        segpfvt: './14-seg-pf-vt.html',
        segvtfvt: './15-seg-vtf-vt.html',
        segapam: './16-seg-ap-am.html',
        segapcta: './17-seg-ap-cta.html',
        segapcancerp: './18-seg-ap-cancerp.html',
        bpoccenter: './bpoccenter.html',
        comprometidossoc: './comprometidossoc.html',
        conocenos: './conocenos.html',
        contactenos: './contactenos.html',
        estudiomercado: './estudiomercado.html',
        faq: './faq.html',
        main: './index.html',
        infoorg: './infoorg.html',
        le: './le.html',
        nuestragente: './nuestragente.html',
        politicas: './politicas.html',
        svgs: './svgs.html',
        trabajaconnosotros: './trabajaconnosotros.html',
        
      }
    }
  },
  server: {
    open: true, // Abre el navegador automáticamente
  }
});
