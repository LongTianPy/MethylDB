var div = document.getElementById('igvDiv');
var options = {
    locus: chromosome.toString() + ":" + start.toString() + "-" + end.toString(),
    reference: {
        id: "hg19",
        fastaURL: "https://s3.amazonaws.com/igv.broadinstitute.org/genomes/seq/1kg_v37/human_g1k_v37_decoy.fasta",
        cytobandURL: "https://s3.amazonaws.com/igv.broadinstitute.org/genomes/seq/b37/b37_cytoband.txt"
    },
    tracks: [
        {
            name: "Genes",
            type: "annotation",
            format: "bed",
            sourceType: "file",
            url: "https://s3.amazonaws.com/igv.broadinstitute.org/annotations/hg19/genes/refGene.hg19.bed.gz",
            indexURL: "https://s3.amazonaws.com/igv.broadinstitute.org/annotations/hg19/genes/refGene.hg19.bed.gz.tbi",
            order: Number.MAX_VALUE,
            visibilityWindow: 300000000,
            displayMode: "EXPANDED"
        },

        {
            name: "DNA Methylation CpG sites",
            format: "vcf",
            url: "/MethylDB/Data/sorted_cpg.txt.gz",
            indexURL:  "/MethylDB/Data/sorted_cpg.txt.gz.tbi",
            type: "variant"
        }
    ]

};
var browser = igv.createBrowser(div, options);
