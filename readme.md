# MethylDB

MethylDB is hosted on a Microsoft Azure VM managed by Mayo Clinic. It is developed as an internship project by [@Long Tian](https://github.com/LongTianPy), supervised by Dr. Zhifu Sun started from May 2018.
It was initially designed as an interface to visualize the DNA methylation data from TCGA. At the time of writing this document, MethylDB is developed as a platform to be able to host different types of data.

## Dependencies
MethylDB is built in LAMP stack: **L**inux OS Ubuntu 17.10 + **A**pache2 + **M**ySQL + **P**HP.
[Bootstrap 4](https://getbootstrap.com/) adds style to web pages. Javascript libraries, such as [Plotly](https://plot.ly/javascript/), [igv.js](https://github.com/igvteam/igv.js), [Simple-statistics](https://github.com/simple-statistics/simple-statistics), and [JStat](https://github.com/jstat/jstat) are used for data visualization and data analysis.
Some python scripts are used for the installation of extra dataset, and this part will be introduced in detail in following sections.