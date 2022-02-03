const Puppeteer = require('puppeteer');
const Fs = require('fs')
const Util = require('util')
const ReadFile = Util.promisify(Fs.readFile)
let request = require('request').defaults({ encoding: null })

class Converter {
    constructor(argv) {
        this.template = argv.template
    }

    async html(filename) {
        try {
            const content = await ReadFile(filename, 'utf8')

            return content
        } catch (error) {
            throw new Error('Cannot read ' + filename)
        }
    }

    generateHeader(argv) {
        return new Promise((resolve, reject) => {
            Fs.readFile('../public/templates-src/templates/' + this.template + '_header.jpg', (err, data) => {
                if (!err) {
                    this.header = "<img style='max-width: 100%;' src='data:image/jpeg;base64," + new Buffer(data).toString('base64') + "'/>"
                    resolve()
                } else {
                    console.log(err);
                }
            });
        })
    }

    generateFooter(argv) {
        return new Promise((resolve, reject) => {
            Fs.readFile('../public/templates-src/templates/' + this.template + '_footer.jpg', (err, data) => {
                if (!err) {
                    this.footer = "<img style='max-width: 100%;' src='data:image/jpeg;base64," + new Buffer(data).toString('base64') + "'/>"
                    resolve()
                } else {
                    console.log(err);
                }
            });
        })
    }

    async pdf(argv) {
        let infilename = argv.infilename
        let outfilename = argv.outfilename
        const html = await this.html(infilename)

        const browser = await Puppeteer.launch()
        const page = await browser.newPage()
        await page.setContent(html, { waitUntil: 'networkidle2', timeout: 5000 });

        if (argv.mode === 'pure') {
            await page.pdf({
                path: outfilename,
                printBackground: true,
                format: 'A4',
                margin: {
                    top: '0px',
                    right: '0px',
                    bottom: '0px',
                    left: '0px'
                }
            });
        } else {
            await page.pdf({
                path: outfilename,
                displayHeaderFooter: true,
                headerTemplate: this.header,
                footerTemplate: this.footer,
                printBackground: true,
                format: 'A4',
                margin: {
                    top: "0px",
                    right: "0px",
                    bottom: "0px",
                    left: "0px"
                }
            });
        }

        await browser.close()

        Fs.unlinkSync(infilename)

        Fs.copyFile(outfilename, argv.path, function(err) {
            if (err) throw err
            Fs.unlinkSync(outfilename)
        })
    }
}
let argv = require('minimist')(process.argv.slice(2));

let converter = new Converter(argv);

if (argv.mode === 'pure') {
    converter.pdf(argv)
} else {
    converter.generateHeader(argv).then(() => {
        converter.generateFooter(argv).then(() => {
            converter.pdf(argv)
        });
    })
}