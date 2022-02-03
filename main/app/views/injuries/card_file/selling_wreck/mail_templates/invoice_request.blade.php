<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Basic Page Needs
    ================================================== -->
    <meta charset="utf-8" />
    <title>
        IdeaLeasing system
    </title>

    <!-- Mobile Specific Metas
    ================================================== -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS
    ================================================== -->

    <!-- Core CSS - Include with every page -->
    <style>
            html {
              font-family: sans-serif;
              -ms-text-size-adjust: 100%;
              -webkit-text-size-adjust: 100%;
            }
            body {
              margin: 0;
            }
            article,
            aside,
            details,
            figcaption,
            figure,
            footer,
            header,
            hgroup,
            main,
            nav,
            section,
            summary {
              display: block;
            }
            audio,
            canvas,
            progress,
            video {
              display: inline-block;
              vertical-align: baseline;
            }
            audio:not([controls]) {
              display: none;
              height: 0;
            }
            [hidden],
            template {
              display: none;
            }
            a {
              background: transparent;
            }
            a:active,
            a:hover {
              outline: 0;
            }
            abbr[title] {
              border-bottom: 1px dotted;
            }
            b,
            strong {
              font-weight: bold;
            }
            dfn {
              font-style: italic;
            }
            h1 {
              font-size: 2em;
              margin: 0.67em 0;
            }
            mark {
              background: #ff0;
              color: #000;
            }
            small {
              font-size: 80%;
            }
            sub,
            sup {
              font-size: 75%;
              line-height: 0;
              position: relative;
              vertical-align: baseline;
            }
            sup {
              top: -0.5em;
            }
            sub {
              bottom: -0.25em;
            }
            img {
              border: 0;
            }
            svg:not(:root) {
              overflow: hidden;
            }
            figure {
              margin: 1em 40px;
            }
            hr {
              -moz-box-sizing: content-box;
              box-sizing: content-box;
              height: 0;
            }
            pre {
              overflow: auto;
            }
            code,
            kbd,
            pre,
            samp {
              font-family: monospace, monospace;
              font-size: 1em;
            }
            button,
            input,
            optgroup,
            select,
            textarea {
              color: inherit;
              font: inherit;
              margin: 0;
            }
            button {
              overflow: visible;
            }
            button,
            select {
              text-transform: none;
            }
            button,
            html input[type="button"],
            input[type="reset"],
            input[type="submit"] {
              -webkit-appearance: button;
              cursor: pointer;
            }
            button[disabled],
            html input[disabled] {
              cursor: default;
            }
            button::-moz-focus-inner,
            input::-moz-focus-inner {
              border: 0;
              padding: 0;
            }
            input {
              line-height: normal;
            }
            input[type="checkbox"],
            input[type="radio"] {
              box-sizing: border-box;
              padding: 0;
            }
            input[type="number"]::-webkit-inner-spin-button,
            input[type="number"]::-webkit-outer-spin-button {
              height: auto;
            }
            input[type="search"] {
              -webkit-appearance: textfield;
              -moz-box-sizing: content-box;
              -webkit-box-sizing: content-box;
              box-sizing: content-box;
            }
            input[type="search"]::-webkit-search-cancel-button,
            input[type="search"]::-webkit-search-decoration {
              -webkit-appearance: none;
            }
            fieldset {
              border: 1px solid #c0c0c0;
              margin: 0 2px;
              padding: 0.35em 0.625em 0.75em;
            }
            legend {
              border: 0;
              padding: 0;
            }
            textarea {
              overflow: auto;
            }
            optgroup {
              font-weight: bold;
            }
            table {
              border-collapse: collapse;
              border-spacing: 0;
            }
            td,
            th {
              padding: 0;
            }
            @media print {
              * {
                text-shadow: none !important;
                color: #000 !important;
                background: transparent !important;
                box-shadow: none !important;
              }
              a,
              a:visited {
                text-decoration: underline;
              }
              a[href]:after {
                content: " (" attr(href) ")";
              }
              abbr[title]:after {
                content: " (" attr(title) ")";
              }
              a[href^="javascript:"]:after,
              a[href^="#"]:after {
                content: "";
              }
              pre,
              blockquote {
                border: 1px solid #999;
                page-break-inside: avoid;
              }
              thead {
                display: table-header-group;
              }
              tr,
              img {
                page-break-inside: avoid;
              }
              img {
                max-width: 100% !important;
              }
              p,
              h2,
              h3 {
                orphans: 3;
                widows: 3;
              }
              h2,
              h3 {
                page-break-after: avoid;
              }
              select {
                background: #fff !important;
              }
              .navbar {
                display: none;
              }
              .table td,
              .table th {
                background-color: #fff !important;
              }
              .btn > .caret,
              .dropup > .btn > .caret {
                border-top-color: #000 !important;
              }
              .label {
                border: 1px solid #000;
              }
              .table {
                border-collapse: collapse !important;
              }
              .table-bordered th,
              .table-bordered td {
                border: 1px solid #ddd !important;
              }
            }
            * {
              -webkit-box-sizing: border-box;
              -moz-box-sizing: border-box;
              box-sizing: border-box;
            }
            *:before,
            *:after {
              -webkit-box-sizing: border-box;
              -moz-box-sizing: border-box;
              box-sizing: border-box;
            }
            html {
              font-size: 10px;
              -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
            }
            body {
              font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
              font-size: 14px;
              line-height: 1.42857143;
              color: #333333;
              background-color: #ffffff;
            }
            input,
            button,
            select,
            textarea {
              font-family: inherit;
              font-size: inherit;
              line-height: inherit;
            }
            a {
              color: #428bca;
              text-decoration: none;
            }
            a:hover,
            a:focus {
              color: #2a6496;
              text-decoration: underline;
            }
            a:focus {
              outline: thin dotted;
              outline: 5px auto -webkit-focus-ring-color;
              outline-offset: -2px;
            }
            figure {
              margin: 0;
            }
            img {
              vertical-align: middle;
            }
            .img-responsive {
              display: block;
              width: 100% \9;
              max-width: 100%;
              height: auto;
            }
            .img-rounded {
              border-radius: 6px;
            }
            .img-thumbnail {
              padding: 4px;
              line-height: 1.42857143;
              background-color: #ffffff;
              border: 1px solid #dddddd;
              border-radius: 4px;
              -webkit-transition: all 0.2s ease-in-out;
              -o-transition: all 0.2s ease-in-out;
              transition: all 0.2s ease-in-out;
              display: inline-block;
              width: 100% \9;
              max-width: 100%;
              height: auto;
            }
            .img-circle {
              border-radius: 50%;
            }
            hr {
              margin-top: 20px;
              margin-bottom: 20px;
              border: 0;
              border-top: 1px solid #eeeeee;
            }
            .sr-only {
              position: absolute;
              width: 1px;
              height: 1px;
              margin: -1px;
              padding: 0;
              overflow: hidden;
              clip: rect(0, 0, 0, 0);
              border: 0;
            }
            .sr-only-focusable:active,
            .sr-only-focusable:focus {
              position: static;
              width: auto;
              height: auto;
              margin: 0;
              overflow: visible;
              clip: auto;
            }
            .container {
              margin-right: auto;
              margin-left: auto;
              padding-left: 15px;
              padding-right: 15px;
            }
            @media (min-width: 768px) {
              .container {
                width: 750px;
              }
            }
            @media (min-width: 992px) {
              .container {
                width: 970px;
              }
            }
            @media (min-width: 1200px) {
              .container {
                width: 1170px;
              }
            }
            .container-fluid {
              margin-right: auto;
              margin-left: auto;
              padding-left: 15px;
              padding-right: 15px;
            }
            .row {
              margin-left: -15px;
              margin-right: -15px;
            }
            .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
              position: relative;
              min-height: 1px;
              padding-left: 15px;
              padding-right: 15px;
            }
            .col-xs-1, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9, .col-xs-10, .col-xs-11, .col-xs-12 {
              float: left;
            }
            .col-xs-12 {
              width: 100%;
            }
            .col-xs-11 {
              width: 91.66666667%;
            }
            .col-xs-10 {
              width: 83.33333333%;
            }
            .col-xs-9 {
              width: 75%;
            }
            .col-xs-8 {
              width: 66.66666667%;
            }
            .col-xs-7 {
              width: 58.33333333%;
            }
            .col-xs-6 {
              width: 50%;
            }
            .col-xs-5 {
              width: 41.66666667%;
            }
            .col-xs-4 {
              width: 33.33333333%;
            }
            .col-xs-3 {
              width: 25%;
            }
            .col-xs-2 {
              width: 16.66666667%;
            }
            .col-xs-1 {
              width: 8.33333333%;
            }
            .col-xs-pull-12 {
              right: 100%;
            }
            .col-xs-pull-11 {
              right: 91.66666667%;
            }
            .col-xs-pull-10 {
              right: 83.33333333%;
            }
            .col-xs-pull-9 {
              right: 75%;
            }
            .col-xs-pull-8 {
              right: 66.66666667%;
            }
            .col-xs-pull-7 {
              right: 58.33333333%;
            }
            .col-xs-pull-6 {
              right: 50%;
            }
            .col-xs-pull-5 {
              right: 41.66666667%;
            }
            .col-xs-pull-4 {
              right: 33.33333333%;
            }
            .col-xs-pull-3 {
              right: 25%;
            }
            .col-xs-pull-2 {
              right: 16.66666667%;
            }
            .col-xs-pull-1 {
              right: 8.33333333%;
            }
            .col-xs-pull-0 {
              right: auto;
            }
            .col-xs-push-12 {
              left: 100%;
            }
            .col-xs-push-11 {
              left: 91.66666667%;
            }
            .col-xs-push-10 {
              left: 83.33333333%;
            }
            .col-xs-push-9 {
              left: 75%;
            }
            .col-xs-push-8 {
              left: 66.66666667%;
            }
            .col-xs-push-7 {
              left: 58.33333333%;
            }
            .col-xs-push-6 {
              left: 50%;
            }
            .col-xs-push-5 {
              left: 41.66666667%;
            }
            .col-xs-push-4 {
              left: 33.33333333%;
            }
            .col-xs-push-3 {
              left: 25%;
            }
            .col-xs-push-2 {
              left: 16.66666667%;
            }
            .col-xs-push-1 {
              left: 8.33333333%;
            }
            .col-xs-push-0 {
              left: auto;
            }
            .col-xs-offset-12 {
              margin-left: 100%;
            }
            .col-xs-offset-11 {
              margin-left: 91.66666667%;
            }
            .col-xs-offset-10 {
              margin-left: 83.33333333%;
            }
            .col-xs-offset-9 {
              margin-left: 75%;
            }
            .col-xs-offset-8 {
              margin-left: 66.66666667%;
            }
            .col-xs-offset-7 {
              margin-left: 58.33333333%;
            }
            .col-xs-offset-6 {
              margin-left: 50%;
            }
            .col-xs-offset-5 {
              margin-left: 41.66666667%;
            }
            .col-xs-offset-4 {
              margin-left: 33.33333333%;
            }
            .col-xs-offset-3 {
              margin-left: 25%;
            }
            .col-xs-offset-2 {
              margin-left: 16.66666667%;
            }
            .col-xs-offset-1 {
              margin-left: 8.33333333%;
            }
            .col-xs-offset-0 {
              margin-left: 0%;
            }
            @media (min-width: 768px) {
              .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12 {
                float: left;
              }
              .col-sm-12 {
                width: 100%;
              }
              .col-sm-11 {
                width: 91.66666667%;
              }
              .col-sm-10 {
                width: 83.33333333%;
              }
              .col-sm-9 {
                width: 75%;
              }
              .col-sm-8 {
                width: 66.66666667%;
              }
              .col-sm-7 {
                width: 58.33333333%;
              }
              .col-sm-6 {
                width: 50%;
              }
              .col-sm-5 {
                width: 41.66666667%;
              }
              .col-sm-4 {
                width: 33.33333333%;
              }
              .col-sm-3 {
                width: 25%;
              }
              .col-sm-2 {
                width: 16.66666667%;
              }
              .col-sm-1 {
                width: 8.33333333%;
              }
              .col-sm-pull-12 {
                right: 100%;
              }
              .col-sm-pull-11 {
                right: 91.66666667%;
              }
              .col-sm-pull-10 {
                right: 83.33333333%;
              }
              .col-sm-pull-9 {
                right: 75%;
              }
              .col-sm-pull-8 {
                right: 66.66666667%;
              }
              .col-sm-pull-7 {
                right: 58.33333333%;
              }
              .col-sm-pull-6 {
                right: 50%;
              }
              .col-sm-pull-5 {
                right: 41.66666667%;
              }
              .col-sm-pull-4 {
                right: 33.33333333%;
              }
              .col-sm-pull-3 {
                right: 25%;
              }
              .col-sm-pull-2 {
                right: 16.66666667%;
              }
              .col-sm-pull-1 {
                right: 8.33333333%;
              }
              .col-sm-pull-0 {
                right: auto;
              }
              .col-sm-push-12 {
                left: 100%;
              }
              .col-sm-push-11 {
                left: 91.66666667%;
              }
              .col-sm-push-10 {
                left: 83.33333333%;
              }
              .col-sm-push-9 {
                left: 75%;
              }
              .col-sm-push-8 {
                left: 66.66666667%;
              }
              .col-sm-push-7 {
                left: 58.33333333%;
              }
              .col-sm-push-6 {
                left: 50%;
              }
              .col-sm-push-5 {
                left: 41.66666667%;
              }
              .col-sm-push-4 {
                left: 33.33333333%;
              }
              .col-sm-push-3 {
                left: 25%;
              }
              .col-sm-push-2 {
                left: 16.66666667%;
              }
              .col-sm-push-1 {
                left: 8.33333333%;
              }
              .col-sm-push-0 {
                left: auto;
              }
              .col-sm-offset-12 {
                margin-left: 100%;
              }
              .col-sm-offset-11 {
                margin-left: 91.66666667%;
              }
              .col-sm-offset-10 {
                margin-left: 83.33333333%;
              }
              .col-sm-offset-9 {
                margin-left: 75%;
              }
              .col-sm-offset-8 {
                margin-left: 66.66666667%;
              }
              .col-sm-offset-7 {
                margin-left: 58.33333333%;
              }
              .col-sm-offset-6 {
                margin-left: 50%;
              }
              .col-sm-offset-5 {
                margin-left: 41.66666667%;
              }
              .col-sm-offset-4 {
                margin-left: 33.33333333%;
              }
              .col-sm-offset-3 {
                margin-left: 25%;
              }
              .col-sm-offset-2 {
                margin-left: 16.66666667%;
              }
              .col-sm-offset-1 {
                margin-left: 8.33333333%;
              }
              .col-sm-offset-0 {
                margin-left: 0%;
              }
            }
            @media (min-width: 992px) {
              .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12 {
                float: left;
              }
              .col-md-12 {
                width: 100%;
              }
              .col-md-11 {
                width: 91.66666667%;
              }
              .col-md-10 {
                width: 83.33333333%;
              }
              .col-md-9 {
                width: 75%;
              }
              .col-md-8 {
                width: 66.66666667%;
              }
              .col-md-7 {
                width: 58.33333333%;
              }
              .col-md-6 {
                width: 50%;
              }
              .col-md-5 {
                width: 41.66666667%;
              }
              .col-md-4 {
                width: 33.33333333%;
              }
              .col-md-3 {
                width: 25%;
              }
              .col-md-2 {
                width: 16.66666667%;
              }
              .col-md-1 {
                width: 8.33333333%;
              }
              .col-md-pull-12 {
                right: 100%;
              }
              .col-md-pull-11 {
                right: 91.66666667%;
              }
              .col-md-pull-10 {
                right: 83.33333333%;
              }
              .col-md-pull-9 {
                right: 75%;
              }
              .col-md-pull-8 {
                right: 66.66666667%;
              }
              .col-md-pull-7 {
                right: 58.33333333%;
              }
              .col-md-pull-6 {
                right: 50%;
              }
              .col-md-pull-5 {
                right: 41.66666667%;
              }
              .col-md-pull-4 {
                right: 33.33333333%;
              }
              .col-md-pull-3 {
                right: 25%;
              }
              .col-md-pull-2 {
                right: 16.66666667%;
              }
              .col-md-pull-1 {
                right: 8.33333333%;
              }
              .col-md-pull-0 {
                right: auto;
              }
              .col-md-push-12 {
                left: 100%;
              }
              .col-md-push-11 {
                left: 91.66666667%;
              }
              .col-md-push-10 {
                left: 83.33333333%;
              }
              .col-md-push-9 {
                left: 75%;
              }
              .col-md-push-8 {
                left: 66.66666667%;
              }
              .col-md-push-7 {
                left: 58.33333333%;
              }
              .col-md-push-6 {
                left: 50%;
              }
              .col-md-push-5 {
                left: 41.66666667%;
              }
              .col-md-push-4 {
                left: 33.33333333%;
              }
              .col-md-push-3 {
                left: 25%;
              }
              .col-md-push-2 {
                left: 16.66666667%;
              }
              .col-md-push-1 {
                left: 8.33333333%;
              }
              .col-md-push-0 {
                left: auto;
              }
              .col-md-offset-12 {
                margin-left: 100%;
              }
              .col-md-offset-11 {
                margin-left: 91.66666667%;
              }
              .col-md-offset-10 {
                margin-left: 83.33333333%;
              }
              .col-md-offset-9 {
                margin-left: 75%;
              }
              .col-md-offset-8 {
                margin-left: 66.66666667%;
              }
              .col-md-offset-7 {
                margin-left: 58.33333333%;
              }
              .col-md-offset-6 {
                margin-left: 50%;
              }
              .col-md-offset-5 {
                margin-left: 41.66666667%;
              }
              .col-md-offset-4 {
                margin-left: 33.33333333%;
              }
              .col-md-offset-3 {
                margin-left: 25%;
              }
              .col-md-offset-2 {
                margin-left: 16.66666667%;
              }
              .col-md-offset-1 {
                margin-left: 8.33333333%;
              }
              .col-md-offset-0 {
                margin-left: 0%;
              }
            }
            @media (min-width: 1200px) {
              .col-lg-1, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-10, .col-lg-11, .col-lg-12 {
                float: left;
              }
              .col-lg-12 {
                width: 100%;
              }
              .col-lg-11 {
                width: 91.66666667%;
              }
              .col-lg-10 {
                width: 83.33333333%;
              }
              .col-lg-9 {
                width: 75%;
              }
              .col-lg-8 {
                width: 66.66666667%;
              }
              .col-lg-7 {
                width: 58.33333333%;
              }
              .col-lg-6 {
                width: 50%;
              }
              .col-lg-5 {
                width: 41.66666667%;
              }
              .col-lg-4 {
                width: 33.33333333%;
              }
              .col-lg-3 {
                width: 25%;
              }
              .col-lg-2 {
                width: 16.66666667%;
              }
              .col-lg-1 {
                width: 8.33333333%;
              }
              .col-lg-pull-12 {
                right: 100%;
              }
              .col-lg-pull-11 {
                right: 91.66666667%;
              }
              .col-lg-pull-10 {
                right: 83.33333333%;
              }
              .col-lg-pull-9 {
                right: 75%;
              }
              .col-lg-pull-8 {
                right: 66.66666667%;
              }
              .col-lg-pull-7 {
                right: 58.33333333%;
              }
              .col-lg-pull-6 {
                right: 50%;
              }
              .col-lg-pull-5 {
                right: 41.66666667%;
              }
              .col-lg-pull-4 {
                right: 33.33333333%;
              }
              .col-lg-pull-3 {
                right: 25%;
              }
              .col-lg-pull-2 {
                right: 16.66666667%;
              }
              .col-lg-pull-1 {
                right: 8.33333333%;
              }
              .col-lg-pull-0 {
                right: auto;
              }
              .col-lg-push-12 {
                left: 100%;
              }
              .col-lg-push-11 {
                left: 91.66666667%;
              }
              .col-lg-push-10 {
                left: 83.33333333%;
              }
              .col-lg-push-9 {
                left: 75%;
              }
              .col-lg-push-8 {
                left: 66.66666667%;
              }
              .col-lg-push-7 {
                left: 58.33333333%;
              }
              .col-lg-push-6 {
                left: 50%;
              }
              .col-lg-push-5 {
                left: 41.66666667%;
              }
              .col-lg-push-4 {
                left: 33.33333333%;
              }
              .col-lg-push-3 {
                left: 25%;
              }
              .col-lg-push-2 {
                left: 16.66666667%;
              }
              .col-lg-push-1 {
                left: 8.33333333%;
              }
              .col-lg-push-0 {
                left: auto;
              }
              .col-lg-offset-12 {
                margin-left: 100%;
              }
              .col-lg-offset-11 {
                margin-left: 91.66666667%;
              }
              .col-lg-offset-10 {
                margin-left: 83.33333333%;
              }
              .col-lg-offset-9 {
                margin-left: 75%;
              }
              .col-lg-offset-8 {
                margin-left: 66.66666667%;
              }
              .col-lg-offset-7 {
                margin-left: 58.33333333%;
              }
              .col-lg-offset-6 {
                margin-left: 50%;
              }
              .col-lg-offset-5 {
                margin-left: 41.66666667%;
              }
              .col-lg-offset-4 {
                margin-left: 33.33333333%;
              }
              .col-lg-offset-3 {
                margin-left: 25%;
              }
              .col-lg-offset-2 {
                margin-left: 16.66666667%;
              }
              .col-lg-offset-1 {
                margin-left: 8.33333333%;
              }
              .col-lg-offset-0 {
                margin-left: 0%;
              }
            }
            table {
              background-color: transparent;
            }
            th {
              text-align: left;
            }
            .table {
              width: 100%;
              max-width: 100%;
              margin-bottom: 20px;
            }
            .table > thead > tr > th,
            .table > tbody > tr > th,
            .table > tfoot > tr > th,
            .table > thead > tr > td,
            .table > tbody > tr > td,
            .table > tfoot > tr > td {
              padding: 8px;
              line-height: 1.42857143;
              vertical-align: top;
              border-top: 1px solid #dddddd;
            }
            .table > thead > tr > th {
              vertical-align: bottom;
              border-bottom: 2px solid #dddddd;
            }
            .table > caption + thead > tr:first-child > th,
            .table > colgroup + thead > tr:first-child > th,
            .table > thead:first-child > tr:first-child > th,
            .table > caption + thead > tr:first-child > td,
            .table > colgroup + thead > tr:first-child > td,
            .table > thead:first-child > tr:first-child > td {
              border-top: 0;
            }
            .table > tbody + tbody {
              border-top: 2px solid #dddddd;
            }
            .table .table {
              background-color: #ffffff;
            }
            .table-condensed > thead > tr > th,
            .table-condensed > tbody > tr > th,
            .table-condensed > tfoot > tr > th,
            .table-condensed > thead > tr > td,
            .table-condensed > tbody > tr > td,
            .table-condensed > tfoot > tr > td {
              padding: 5px;
            }
            .table-bordered {
              border: 1px solid #dddddd;
            }
            .table-bordered > thead > tr > th,
            .table-bordered > tbody > tr > th,
            .table-bordered > tfoot > tr > th,
            .table-bordered > thead > tr > td,
            .table-bordered > tbody > tr > td,
            .table-bordered > tfoot > tr > td {
              border: 1px solid #dddddd;
            }
            .table-bordered > thead > tr > th,
            .table-bordered > thead > tr > td {
              border-bottom-width: 2px;
            }
            .table-striped > tbody > tr:nth-child(odd) > td,
            .table-striped > tbody > tr:nth-child(odd) > th {
              background-color: #f9f9f9;
            }
            .table-hover > tbody > tr:hover > td,
            .table-hover > tbody > tr:hover > th {
              background-color: #f5f5f5;
            }
            table col[class*="col-"] {
              position: static;
              float: none;
              display: table-column;
            }
            table td[class*="col-"],
            table th[class*="col-"] {
              position: static;
              float: none;
              display: table-cell;
            }
            .table > thead > tr > td.active,
            .table > tbody > tr > td.active,
            .table > tfoot > tr > td.active,
            .table > thead > tr > th.active,
            .table > tbody > tr > th.active,
            .table > tfoot > tr > th.active,
            .table > thead > tr.active > td,
            .table > tbody > tr.active > td,
            .table > tfoot > tr.active > td,
            .table > thead > tr.active > th,
            .table > tbody > tr.active > th,
            .table > tfoot > tr.active > th {
              background-color: #f5f5f5;
            }
            .table-hover > tbody > tr > td.active:hover,
            .table-hover > tbody > tr > th.active:hover,
            .table-hover > tbody > tr.active:hover > td,
            .table-hover > tbody > tr:hover > .active,
            .table-hover > tbody > tr.active:hover > th {
              background-color: #e8e8e8;
            }
            .table > thead > tr > td.success,
            .table > tbody > tr > td.success,
            .table > tfoot > tr > td.success,
            .table > thead > tr > th.success,
            .table > tbody > tr > th.success,
            .table > tfoot > tr > th.success,
            .table > thead > tr.success > td,
            .table > tbody > tr.success > td,
            .table > tfoot > tr.success > td,
            .table > thead > tr.success > th,
            .table > tbody > tr.success > th,
            .table > tfoot > tr.success > th {
              background-color: #dff0d8;
            }
            .table-hover > tbody > tr > td.success:hover,
            .table-hover > tbody > tr > th.success:hover,
            .table-hover > tbody > tr.success:hover > td,
            .table-hover > tbody > tr:hover > .success,
            .table-hover > tbody > tr.success:hover > th {
              background-color: #d0e9c6;
            }
            .table > thead > tr > td.info,
            .table > tbody > tr > td.info,
            .table > tfoot > tr > td.info,
            .table > thead > tr > th.info,
            .table > tbody > tr > th.info,
            .table > tfoot > tr > th.info,
            .table > thead > tr.info > td,
            .table > tbody > tr.info > td,
            .table > tfoot > tr.info > td,
            .table > thead > tr.info > th,
            .table > tbody > tr.info > th,
            .table > tfoot > tr.info > th {
              background-color: #d9edf7;
            }
            .table-hover > tbody > tr > td.info:hover,
            .table-hover > tbody > tr > th.info:hover,
            .table-hover > tbody > tr.info:hover > td,
            .table-hover > tbody > tr:hover > .info,
            .table-hover > tbody > tr.info:hover > th {
              background-color: #c4e3f3;
            }
            .table > thead > tr > td.warning,
            .table > tbody > tr > td.warning,
            .table > tfoot > tr > td.warning,
            .table > thead > tr > th.warning,
            .table > tbody > tr > th.warning,
            .table > tfoot > tr > th.warning,
            .table > thead > tr.warning > td,
            .table > tbody > tr.warning > td,
            .table > tfoot > tr.warning > td,
            .table > thead > tr.warning > th,
            .table > tbody > tr.warning > th,
            .table > tfoot > tr.warning > th {
              background-color: #fcf8e3;
            }
            .table-hover > tbody > tr > td.warning:hover,
            .table-hover > tbody > tr > th.warning:hover,
            .table-hover > tbody > tr.warning:hover > td,
            .table-hover > tbody > tr:hover > .warning,
            .table-hover > tbody > tr.warning:hover > th {
              background-color: #faf2cc;
            }
            .table > thead > tr > td.danger,
            .table > tbody > tr > td.danger,
            .table > tfoot > tr > td.danger,
            .table > thead > tr > th.danger,
            .table > tbody > tr > th.danger,
            .table > tfoot > tr > th.danger,
            .table > thead > tr.danger > td,
            .table > tbody > tr.danger > td,
            .table > tfoot > tr.danger > td,
            .table > thead > tr.danger > th,
            .table > tbody > tr.danger > th,
            .table > tfoot > tr.danger > th {
              background-color: #f2dede;
            }
            .table-hover > tbody > tr > td.danger:hover,
            .table-hover > tbody > tr > th.danger:hover,
            .table-hover > tbody > tr.danger:hover > td,
            .table-hover > tbody > tr:hover > .danger,
            .table-hover > tbody > tr.danger:hover > th {
              background-color: #ebcccc;
            }
            @media screen and (max-width: 767px) {
              .table-responsive {
                width: 100%;
                margin-bottom: 15px;
                overflow-y: hidden;
                overflow-x: auto;
                -ms-overflow-style: -ms-autohiding-scrollbar;
                border: 1px solid #dddddd;
                -webkit-overflow-scrolling: touch;
              }
              .table-responsive > .table {
                margin-bottom: 0;
              }
              .table-responsive > .table > thead > tr > th,
              .table-responsive > .table > tbody > tr > th,
              .table-responsive > .table > tfoot > tr > th,
              .table-responsive > .table > thead > tr > td,
              .table-responsive > .table > tbody > tr > td,
              .table-responsive > .table > tfoot > tr > td {
                white-space: nowrap;
              }
              .table-responsive > .table-bordered {
                border: 0;
              }
              .table-responsive > .table-bordered > thead > tr > th:first-child,
              .table-responsive > .table-bordered > tbody > tr > th:first-child,
              .table-responsive > .table-bordered > tfoot > tr > th:first-child,
              .table-responsive > .table-bordered > thead > tr > td:first-child,
              .table-responsive > .table-bordered > tbody > tr > td:first-child,
              .table-responsive > .table-bordered > tfoot > tr > td:first-child {
                border-left: 0;
              }
              .table-responsive > .table-bordered > thead > tr > th:last-child,
              .table-responsive > .table-bordered > tbody > tr > th:last-child,
              .table-responsive > .table-bordered > tfoot > tr > th:last-child,
              .table-responsive > .table-bordered > thead > tr > td:last-child,
              .table-responsive > .table-bordered > tbody > tr > td:last-child,
              .table-responsive > .table-bordered > tfoot > tr > td:last-child {
                border-right: 0;
              }
              .table-responsive > .table-bordered > tbody > tr:last-child > th,
              .table-responsive > .table-bordered > tfoot > tr:last-child > th,
              .table-responsive > .table-bordered > tbody > tr:last-child > td,
              .table-responsive > .table-bordered > tfoot > tr:last-child > td {
                border-bottom: 0;
              }
            }
            fieldset {
              padding: 0;
              margin: 0;
              border: 0;
              min-width: 0;
            }
            legend {
              display: block;
              width: 100%;
              padding: 0;
              margin-bottom: 20px;
              font-size: 21px;
              line-height: inherit;
              color: #333333;
              border: 0;
              border-bottom: 1px solid #e5e5e5;
            }
            label {
              display: inline-block;
              max-width: 100%;
              margin-bottom: 5px;
              font-weight: bold;
            }
            input[type="search"] {
              -webkit-box-sizing: border-box;
              -moz-box-sizing: border-box;
              box-sizing: border-box;
            }
            input[type="radio"],
            input[type="checkbox"] {
              margin: 4px 0 0;
              margin-top: 1px \9;
              line-height: normal;
            }
            input[type="file"] {
              display: block;
            }
            input[type="range"] {
              display: block;
              width: 100%;
            }
            select[multiple],
            select[size] {
              height: auto;
            }
            input[type="file"]:focus,
            input[type="radio"]:focus,
            input[type="checkbox"]:focus {
              outline: thin dotted;
              outline: 5px auto -webkit-focus-ring-color;
              outline-offset: -2px;
            }
            output {
              display: block;
              padding-top: 7px;
              font-size: 14px;
              line-height: 1.42857143;
              color: #555555;
            }
            .form-control {
              display: block;
              width: 100%;
              height: 34px;
              padding: 6px 12px;
              font-size: 14px;
              line-height: 1.42857143;
              color: #555555;
              background-color: #ffffff;
              background-image: none;
              border: 1px solid #cccccc;
              border-radius: 4px;
              -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
              box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
              -webkit-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
              -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
              transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
            }
            .form-control:focus {
              border-color: #66afe9;
              outline: 0;
              -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, 0.6);
              box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, 0.6);
            }
            .form-control::-moz-placeholder {
              color: #777777;
              opacity: 1;
            }
            .form-control:-ms-input-placeholder {
              color: #777777;
            }
            .form-control::-webkit-input-placeholder {
              color: #777777;
            }
            .form-control[disabled],
            .form-control[readonly],
            fieldset[disabled] .form-control {
              cursor: not-allowed;
              background-color: #eeeeee;
              opacity: 1;
            }
            textarea.form-control {
              height: auto;
            }
            input[type="search"] {
              -webkit-appearance: none;
            }
            input[type="date"],
            input[type="time"],
            input[type="datetime-local"],
            input[type="month"] {
              line-height: 34px;
              line-height: 1.42857143 \0;
            }
            input[type="date"].input-sm,
            input[type="time"].input-sm,
            input[type="datetime-local"].input-sm,
            input[type="month"].input-sm {
              line-height: 30px;
            }
            input[type="date"].input-lg,
            input[type="time"].input-lg,
            input[type="datetime-local"].input-lg,
            input[type="month"].input-lg {
              line-height: 46px;
            }
            .form-group {
              margin-bottom: 15px;
            }
            .radio,
            .checkbox {
              position: relative;
              display: block;
              min-height: 20px;
              margin-top: 10px;
              margin-bottom: 10px;
            }
            .radio label,
            .checkbox label {
              padding-left: 20px;
              margin-bottom: 0;
              font-weight: normal;
              cursor: pointer;
            }
            .radio input[type="radio"],
            .radio-inline input[type="radio"],
            .checkbox input[type="checkbox"],
            .checkbox-inline input[type="checkbox"] {
              position: absolute;
              margin-left: -20px;
              margin-top: 4px \9;
            }
            .radio + .radio,
            .checkbox + .checkbox {
              margin-top: -5px;
            }
            .radio-inline,
            .checkbox-inline {
              display: inline-block;
              padding-left: 20px;
              margin-bottom: 0;
              vertical-align: middle;
              font-weight: normal;
              cursor: pointer;
            }
            .radio-inline + .radio-inline,
            .checkbox-inline + .checkbox-inline {
              margin-top: 0;
              margin-left: 10px;
            }
            input[type="radio"][disabled],
            input[type="checkbox"][disabled],
            input[type="radio"].disabled,
            input[type="checkbox"].disabled,
            fieldset[disabled] input[type="radio"],
            fieldset[disabled] input[type="checkbox"] {
              cursor: not-allowed;
            }
            .radio-inline.disabled,
            .checkbox-inline.disabled,
            fieldset[disabled] .radio-inline,
            fieldset[disabled] .checkbox-inline {
              cursor: not-allowed;
            }
            .radio.disabled label,
            .checkbox.disabled label,
            fieldset[disabled] .radio label,
            fieldset[disabled] .checkbox label {
              cursor: not-allowed;
            }
            .form-control-static {
              padding-top: 7px;
              padding-bottom: 7px;
              margin-bottom: 0;
            }
            .form-control-static.input-lg,
            .form-control-static.input-sm {
              padding-left: 0;
              padding-right: 0;
            }
            .input-sm,
            .form-horizontal .form-group-sm .form-control {
              height: 30px;
              padding: 5px 10px;
              font-size: 12px;
              line-height: 1.5;
              border-radius: 3px;
            }
            select.input-sm {
              height: 30px;
              line-height: 30px;
            }
            textarea.input-sm,
            select[multiple].input-sm {
              height: auto;
            }
            .input-lg,
            .form-horizontal .form-group-lg .form-control {
              height: 46px;
              padding: 10px 16px;
              font-size: 18px;
              line-height: 1.33;
              border-radius: 6px;
            }
            select.input-lg {
              height: 46px;
              line-height: 46px;
            }
            textarea.input-lg,
            select[multiple].input-lg {
              height: auto;
            }
            .has-feedback {
              position: relative;
            }
            .has-feedback .form-control {
              padding-right: 42.5px;
            }
            .form-control-feedback {
              position: absolute;
              top: 25px;
              right: 0;
              z-index: 2;
              display: block;
              width: 34px;
              height: 34px;
              line-height: 34px;
              text-align: center;
            }
            .input-lg + .form-control-feedback {
              width: 46px;
              height: 46px;
              line-height: 46px;
            }
            .input-sm + .form-control-feedback {
              width: 30px;
              height: 30px;
              line-height: 30px;
            }
            .has-success .help-block,
            .has-success .control-label,
            .has-success .radio,
            .has-success .checkbox,
            .has-success .radio-inline,
            .has-success .checkbox-inline {
              color: #3c763d;
            }
            .has-success .form-control {
              border-color: #3c763d;
              -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
              box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
            }
            .has-success .form-control:focus {
              border-color: #2b542c;
              -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #67b168;
              box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #67b168;
            }
            .has-success .input-group-addon {
              color: #3c763d;
              border-color: #3c763d;
              background-color: #dff0d8;
            }
            .has-success .form-control-feedback {
              color: #3c763d;
            }
            .has-warning .help-block,
            .has-warning .control-label,
            .has-warning .radio,
            .has-warning .checkbox,
            .has-warning .radio-inline,
            .has-warning .checkbox-inline {
              color: #8a6d3b;
            }
            .has-warning .form-control {
              border-color: #8a6d3b;
              -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
              box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
            }
            .has-warning .form-control:focus {
              border-color: #66512c;
              -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #c0a16b;
              box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #c0a16b;
            }
            .has-warning .input-group-addon {
              color: #8a6d3b;
              border-color: #8a6d3b;
              background-color: #fcf8e3;
            }
            .has-warning .form-control-feedback {
              color: #8a6d3b;
            }
            .has-error .help-block,
            .has-error .control-label,
            .has-error .radio,
            .has-error .checkbox,
            .has-error .radio-inline,
            .has-error .checkbox-inline {
              color: #a94442;
            }
            .has-error .form-control {
              border-color: #a94442;
              -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
              box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
            }
            .has-error .form-control:focus {
              border-color: #843534;
              -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #ce8483;
              box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #ce8483;
            }
            .has-error .input-group-addon {
              color: #a94442;
              border-color: #a94442;
              background-color: #f2dede;
            }
            .has-error .form-control-feedback {
              color: #a94442;
            }
            .has-feedback label.sr-only ~ .form-control-feedback {
              top: 0;
            }
            .help-block {
              display: block;
              margin-top: 5px;
              margin-bottom: 10px;
              color: #737373;
            }
            @media (min-width: 768px) {
              .form-inline .form-group {
                display: inline-block;
                margin-bottom: 0;
                vertical-align: middle;
              }
              .form-inline .form-control {
                display: inline-block;
                width: auto;
                vertical-align: middle;
              }
              .form-inline .input-group {
                display: inline-table;
                vertical-align: middle;
              }
              .form-inline .input-group .input-group-addon,
              .form-inline .input-group .input-group-btn,
              .form-inline .input-group .form-control {
                width: auto;
              }
              .form-inline .input-group > .form-control {
                width: 100%;
              }
              .form-inline .control-label {
                margin-bottom: 0;
                vertical-align: middle;
              }
              .form-inline .radio,
              .form-inline .checkbox {
                display: inline-block;
                margin-top: 0;
                margin-bottom: 0;
                vertical-align: middle;
              }
              .form-inline .radio label,
              .form-inline .checkbox label {
                padding-left: 0;
              }
              .form-inline .radio input[type="radio"],
              .form-inline .checkbox input[type="checkbox"] {
                position: relative;
                margin-left: 0;
              }
              .form-inline .has-feedback .form-control-feedback {
                top: 0;
              }
            }
            .form-horizontal .radio,
            .form-horizontal .checkbox,
            .form-horizontal .radio-inline,
            .form-horizontal .checkbox-inline {
              margin-top: 0;
              margin-bottom: 0;
              padding-top: 7px;
            }
            .form-horizontal .radio,
            .form-horizontal .checkbox {
              min-height: 27px;
            }
            .form-horizontal .form-group {
              margin-left: -15px;
              margin-right: -15px;
            }
            @media (min-width: 768px) {
              .form-horizontal .control-label {
                text-align: right;
                margin-bottom: 0;
                padding-top: 7px;
              }
            }
            .form-horizontal .has-feedback .form-control-feedback {
              top: 0;
              right: 15px;
            }
            @media (min-width: 768px) {
              .form-horizontal .form-group-lg .control-label {
                padding-top: 14.3px;
              }
            }
            @media (min-width: 768px) {
              .form-horizontal .form-group-sm .control-label {
                padding-top: 6px;
              }
            }
            .btn {
              display: inline-block;
              margin-bottom: 0;
              font-weight: normal;
              text-align: center;
              vertical-align: middle;
              cursor: pointer;
              background-image: none;
              border: 1px solid transparent;
              white-space: nowrap;
              padding: 6px 12px;
              font-size: 14px;
              line-height: 1.42857143;
              border-radius: 4px;
              -webkit-user-select: none;
              -moz-user-select: none;
              -ms-user-select: none;
              user-select: none;
            }
            .btn:focus,
            .btn:active:focus,
            .btn.active:focus {
              outline: thin dotted;
              outline: 5px auto -webkit-focus-ring-color;
              outline-offset: -2px;
            }
            .btn:hover,
            .btn:focus {
              color: #333333;
              text-decoration: none;
            }
            .btn:active,
            .btn.active {
              outline: 0;
              background-image: none;
              -webkit-box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
              box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
            }
            .btn.disabled,
            .btn[disabled],
            fieldset[disabled] .btn {
              cursor: not-allowed;
              pointer-events: none;
              opacity: 0.65;
              filter: alpha(opacity=65);
              -webkit-box-shadow: none;
              box-shadow: none;
            }
            .btn-default {
              color: #333333;
              background-color: #ffffff;
              border-color: #cccccc;
            }
            .btn-default:hover,
            .btn-default:focus,
            .btn-default:active,
            .btn-default.active,
            .open > .dropdown-toggle.btn-default {
              color: #333333;
              background-color: #e6e6e6;
              border-color: #adadad;
            }
            .btn-default:active,
            .btn-default.active,
            .open > .dropdown-toggle.btn-default {
              background-image: none;
            }
            .btn-default.disabled,
            .btn-default[disabled],
            fieldset[disabled] .btn-default,
            .btn-default.disabled:hover,
            .btn-default[disabled]:hover,
            fieldset[disabled] .btn-default:hover,
            .btn-default.disabled:focus,
            .btn-default[disabled]:focus,
            fieldset[disabled] .btn-default:focus,
            .btn-default.disabled:active,
            .btn-default[disabled]:active,
            fieldset[disabled] .btn-default:active,
            .btn-default.disabled.active,
            .btn-default[disabled].active,
            fieldset[disabled] .btn-default.active {
              background-color: #ffffff;
              border-color: #cccccc;
            }
            .btn-default .badge {
              color: #ffffff;
              background-color: #333333;
            }
            .btn-primary {
              color: #ffffff;
              background-color: #428bca;
              border-color: #357ebd;
            }
            .btn-primary:hover,
            .btn-primary:focus,
            .btn-primary:active,
            .btn-primary.active,
            .open > .dropdown-toggle.btn-primary {
              color: #ffffff;
              background-color: #3071a9;
              border-color: #285e8e;
            }
            .btn-primary:active,
            .btn-primary.active,
            .open > .dropdown-toggle.btn-primary {
              background-image: none;
            }
            .btn-primary.disabled,
            .btn-primary[disabled],
            fieldset[disabled] .btn-primary,
            .btn-primary.disabled:hover,
            .btn-primary[disabled]:hover,
            fieldset[disabled] .btn-primary:hover,
            .btn-primary.disabled:focus,
            .btn-primary[disabled]:focus,
            fieldset[disabled] .btn-primary:focus,
            .btn-primary.disabled:active,
            .btn-primary[disabled]:active,
            fieldset[disabled] .btn-primary:active,
            .btn-primary.disabled.active,
            .btn-primary[disabled].active,
            fieldset[disabled] .btn-primary.active {
              background-color: #428bca;
              border-color: #357ebd;
            }
            .btn-primary .badge {
              color: #428bca;
              background-color: #ffffff;
            }
            .btn-success {
              color: #ffffff;
              background-color: #5cb85c;
              border-color: #4cae4c;
            }
            .btn-success:hover,
            .btn-success:focus,
            .btn-success:active,
            .btn-success.active,
            .open > .dropdown-toggle.btn-success {
              color: #ffffff;
              background-color: #449d44;
              border-color: #398439;
            }
            .btn-success:active,
            .btn-success.active,
            .open > .dropdown-toggle.btn-success {
              background-image: none;
            }
            .btn-success.disabled,
            .btn-success[disabled],
            fieldset[disabled] .btn-success,
            .btn-success.disabled:hover,
            .btn-success[disabled]:hover,
            fieldset[disabled] .btn-success:hover,
            .btn-success.disabled:focus,
            .btn-success[disabled]:focus,
            fieldset[disabled] .btn-success:focus,
            .btn-success.disabled:active,
            .btn-success[disabled]:active,
            fieldset[disabled] .btn-success:active,
            .btn-success.disabled.active,
            .btn-success[disabled].active,
            fieldset[disabled] .btn-success.active {
              background-color: #5cb85c;
              border-color: #4cae4c;
            }
            .btn-success .badge {
              color: #5cb85c;
              background-color: #ffffff;
            }
            .btn-info {
              color: #ffffff;
              background-color: #5bc0de;
              border-color: #46b8da;
            }
            .btn-info:hover,
            .btn-info:focus,
            .btn-info:active,
            .btn-info.active,
            .open > .dropdown-toggle.btn-info {
              color: #ffffff;
              background-color: #31b0d5;
              border-color: #269abc;
            }
            .btn-info:active,
            .btn-info.active,
            .open > .dropdown-toggle.btn-info {
              background-image: none;
            }
            .btn-info.disabled,
            .btn-info[disabled],
            fieldset[disabled] .btn-info,
            .btn-info.disabled:hover,
            .btn-info[disabled]:hover,
            fieldset[disabled] .btn-info:hover,
            .btn-info.disabled:focus,
            .btn-info[disabled]:focus,
            fieldset[disabled] .btn-info:focus,
            .btn-info.disabled:active,
            .btn-info[disabled]:active,
            fieldset[disabled] .btn-info:active,
            .btn-info.disabled.active,
            .btn-info[disabled].active,
            fieldset[disabled] .btn-info.active {
              background-color: #5bc0de;
              border-color: #46b8da;
            }
            .btn-info .badge {
              color: #5bc0de;
              background-color: #ffffff;
            }
            .btn-warning {
              color: #ffffff;
              background-color: #f0ad4e;
              border-color: #eea236;
            }
            .btn-warning:hover,
            .btn-warning:focus,
            .btn-warning:active,
            .btn-warning.active,
            .open > .dropdown-toggle.btn-warning {
              color: #ffffff;
              background-color: #ec971f;
              border-color: #d58512;
            }
            .btn-warning:active,
            .btn-warning.active,
            .open > .dropdown-toggle.btn-warning {
              background-image: none;
            }
            .btn-warning.disabled,
            .btn-warning[disabled],
            fieldset[disabled] .btn-warning,
            .btn-warning.disabled:hover,
            .btn-warning[disabled]:hover,
            fieldset[disabled] .btn-warning:hover,
            .btn-warning.disabled:focus,
            .btn-warning[disabled]:focus,
            fieldset[disabled] .btn-warning:focus,
            .btn-warning.disabled:active,
            .btn-warning[disabled]:active,
            fieldset[disabled] .btn-warning:active,
            .btn-warning.disabled.active,
            .btn-warning[disabled].active,
            fieldset[disabled] .btn-warning.active {
              background-color: #f0ad4e;
              border-color: #eea236;
            }
            .btn-warning .badge {
              color: #f0ad4e;
              background-color: #ffffff;
            }
            .btn-danger {
              color: #ffffff;
              background-color: #d9534f;
              border-color: #d43f3a;
            }
            .btn-danger:hover,
            .btn-danger:focus,
            .btn-danger:active,
            .btn-danger.active,
            .open > .dropdown-toggle.btn-danger {
              color: #ffffff;
              background-color: #c9302c;
              border-color: #ac2925;
            }
            .btn-danger:active,
            .btn-danger.active,
            .open > .dropdown-toggle.btn-danger {
              background-image: none;
            }
            .btn-danger.disabled,
            .btn-danger[disabled],
            fieldset[disabled] .btn-danger,
            .btn-danger.disabled:hover,
            .btn-danger[disabled]:hover,
            fieldset[disabled] .btn-danger:hover,
            .btn-danger.disabled:focus,
            .btn-danger[disabled]:focus,
            fieldset[disabled] .btn-danger:focus,
            .btn-danger.disabled:active,
            .btn-danger[disabled]:active,
            fieldset[disabled] .btn-danger:active,
            .btn-danger.disabled.active,
            .btn-danger[disabled].active,
            fieldset[disabled] .btn-danger.active {
              background-color: #d9534f;
              border-color: #d43f3a;
            }
            .btn-danger .badge {
              color: #d9534f;
              background-color: #ffffff;
            }
            .btn-link {
              color: #428bca;
              font-weight: normal;
              cursor: pointer;
              border-radius: 0;
            }
            .btn-link,
            .btn-link:active,
            .btn-link[disabled],
            fieldset[disabled] .btn-link {
              background-color: transparent;
              -webkit-box-shadow: none;
              box-shadow: none;
            }
            .btn-link,
            .btn-link:hover,
            .btn-link:focus,
            .btn-link:active {
              border-color: transparent;
            }
            .btn-link:hover,
            .btn-link:focus {
              color: #2a6496;
              text-decoration: underline;
              background-color: transparent;
            }
            .btn-link[disabled]:hover,
            fieldset[disabled] .btn-link:hover,
            .btn-link[disabled]:focus,
            fieldset[disabled] .btn-link:focus {
              color: #777777;
              text-decoration: none;
            }
            .btn-lg {
              padding: 10px 16px;
              font-size: 18px;
              line-height: 1.33;
              border-radius: 6px;
            }
            .btn-sm {
              padding: 5px 10px;
              font-size: 12px;
              line-height: 1.5;
              border-radius: 3px;
            }
            .btn-xs {
              padding: 1px 5px;
              font-size: 12px;
              line-height: 1.5;
              border-radius: 3px;
            }
            .btn-block {
              display: block;
              width: 100%;
            }
            .btn-block + .btn-block {
              margin-top: 5px;
            }
            input[type="submit"].btn-block,
            input[type="reset"].btn-block,
            input[type="button"].btn-block {
              width: 100%;
            }
            .nav {
              margin-bottom: 0;
              padding-left: 0;
              list-style: none;
            }
            .nav > li {
              position: relative;
              display: block;
            }
            .nav > li > a {
              position: relative;
              display: block;
              padding: 10px 15px;
            }
            .nav > li > a:hover,
            .nav > li > a:focus {
              text-decoration: none;
              background-color: #eeeeee;
            }
            .nav > li.disabled > a {
              color: #777777;
            }
            .nav > li.disabled > a:hover,
            .nav > li.disabled > a:focus {
              color: #777777;
              text-decoration: none;
              background-color: transparent;
              cursor: not-allowed;
            }
            .nav .open > a,
            .nav .open > a:hover,
            .nav .open > a:focus {
              background-color: #eeeeee;
              border-color: #428bca;
            }
            .nav .nav-divider {
              height: 1px;
              margin: 9px 0;
              overflow: hidden;
              background-color: #e5e5e5;
            }
            .nav > li > a > img {
              max-width: none;
            }
            .nav-tabs {
              border-bottom: 1px solid #dddddd;
            }
            .nav-tabs > li {
              float: left;
              margin-bottom: -1px;
            }
            .nav-tabs > li > a {
              margin-right: 2px;
              line-height: 1.42857143;
              border: 1px solid transparent;
              border-radius: 4px 4px 0 0;
            }
            .nav-tabs > li > a:hover {
              border-color: #eeeeee #eeeeee #dddddd;
            }
            .nav-tabs > li.active > a,
            .nav-tabs > li.active > a:hover,
            .nav-tabs > li.active > a:focus {
              color: #555555;
              background-color: #ffffff;
              border: 1px solid #dddddd;
              border-bottom-color: transparent;
              cursor: default;
            }
            .nav-tabs.nav-justified {
              width: 100%;
              border-bottom: 0;
            }
            .nav-tabs.nav-justified > li {
              float: none;
            }
            .nav-tabs.nav-justified > li > a {
              text-align: center;
              margin-bottom: 5px;
            }
            .nav-tabs.nav-justified > .dropdown .dropdown-menu {
              top: auto;
              left: auto;
            }
            @media (min-width: 768px) {
              .nav-tabs.nav-justified > li {
                display: table-cell;
                width: 1%;
              }
              .nav-tabs.nav-justified > li > a {
                margin-bottom: 0;
              }
            }
            .nav-tabs.nav-justified > li > a {
              margin-right: 0;
              border-radius: 4px;
            }
            .nav-tabs.nav-justified > .active > a,
            .nav-tabs.nav-justified > .active > a:hover,
            .nav-tabs.nav-justified > .active > a:focus {
              border: 1px solid #dddddd;
            }
            @media (min-width: 768px) {
              .nav-tabs.nav-justified > li > a {
                border-bottom: 1px solid #dddddd;
                border-radius: 4px 4px 0 0;
              }
              .nav-tabs.nav-justified > .active > a,
              .nav-tabs.nav-justified > .active > a:hover,
              .nav-tabs.nav-justified > .active > a:focus {
                border-bottom-color: #ffffff;
              }
            }
            .nav-pills > li {
              float: left;
            }
            .nav-pills > li > a {
              border-radius: 4px;
            }
            .nav-pills > li + li {
              margin-left: 2px;
            }
            .nav-pills > li.active > a,
            .nav-pills > li.active > a:hover,
            .nav-pills > li.active > a:focus {
              color: #ffffff;
              background-color: #428bca;
            }
            .nav-stacked > li {
              float: none;
            }
            .nav-stacked > li + li {
              margin-top: 2px;
              margin-left: 0;
            }
            .nav-justified {
              width: 100%;
            }
            .nav-justified > li {
              float: none;
            }
            .nav-justified > li > a {
              text-align: center;
              margin-bottom: 5px;
            }
            .nav-justified > .dropdown .dropdown-menu {
              top: auto;
              left: auto;
            }
            @media (min-width: 768px) {
              .nav-justified > li {
                display: table-cell;
                width: 1%;
              }
              .nav-justified > li > a {
                margin-bottom: 0;
              }
            }
            .nav-tabs-justified {
              border-bottom: 0;
            }
            .nav-tabs-justified > li > a {
              margin-right: 0;
              border-radius: 4px;
            }
            .nav-tabs-justified > .active > a,
            .nav-tabs-justified > .active > a:hover,
            .nav-tabs-justified > .active > a:focus {
              border: 1px solid #dddddd;
            }
            @media (min-width: 768px) {
              .nav-tabs-justified > li > a {
                border-bottom: 1px solid #dddddd;
                border-radius: 4px 4px 0 0;
              }
              .nav-tabs-justified > .active > a,
              .nav-tabs-justified > .active > a:hover,
              .nav-tabs-justified > .active > a:focus {
                border-bottom-color: #ffffff;
              }
            }
            .tab-content > .tab-pane {
              display: none;
            }
            .tab-content > .active {
              display: block;
            }
            .nav-tabs .dropdown-menu {
              margin-top: -1px;
              border-top-right-radius: 0;
              border-top-left-radius: 0;
            }
            .navbar {
              position: relative;
              min-height: 50px;
              margin-bottom: 20px;
              border: 1px solid transparent;
            }
            @media (min-width: 768px) {
              .navbar {
                border-radius: 4px;
              }
            }
            @media (min-width: 768px) {
              .navbar-header {
                float: left;
              }
            }
            .navbar-collapse {
              overflow-x: visible;
              padding-right: 15px;
              padding-left: 15px;
              border-top: 1px solid transparent;
              box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1);
              -webkit-overflow-scrolling: touch;
            }
            .navbar-collapse.in {
              overflow-y: auto;
            }
            @media (min-width: 768px) {
              .navbar-collapse {
                width: auto;
                border-top: 0;
                box-shadow: none;
              }
              .navbar-collapse.collapse {
                display: block !important;
                height: auto !important;
                padding-bottom: 0;
                overflow: visible !important;
              }
              .navbar-collapse.in {
                overflow-y: visible;
              }
              .navbar-fixed-top .navbar-collapse,
              .navbar-static-top .navbar-collapse,
              .navbar-fixed-bottom .navbar-collapse {
                padding-left: 0;
                padding-right: 0;
              }
            }
            .navbar-fixed-top .navbar-collapse,
            .navbar-fixed-bottom .navbar-collapse {
              max-height: 340px;
            }
            @media (max-width: 480px) and (orientation: landscape) {
              .navbar-fixed-top .navbar-collapse,
              .navbar-fixed-bottom .navbar-collapse {
                max-height: 200px;
              }
            }
            .container > .navbar-header,
            .container-fluid > .navbar-header,
            .container > .navbar-collapse,
            .container-fluid > .navbar-collapse {
              margin-right: -15px;
              margin-left: -15px;
            }
            @media (min-width: 768px) {
              .container > .navbar-header,
              .container-fluid > .navbar-header,
              .container > .navbar-collapse,
              .container-fluid > .navbar-collapse {
                margin-right: 0;
                margin-left: 0;
              }
            }
            .navbar-static-top {
              z-index: 1000;
              border-width: 0 0 1px;
            }
            @media (min-width: 768px) {
              .navbar-static-top {
                border-radius: 0;
              }
            }
            .navbar-fixed-top,
            .navbar-fixed-bottom {
              position: fixed;
              right: 0;
              left: 0;
              z-index: 1030;
              -webkit-transform: translate3d(0, 0, 0);
              transform: translate3d(0, 0, 0);
            }
            @media (min-width: 768px) {
              .navbar-fixed-top,
              .navbar-fixed-bottom {
                border-radius: 0;
              }
            }
            .navbar-fixed-top {
              top: 0;
              border-width: 0 0 1px;
            }
            .navbar-fixed-bottom {
              bottom: 0;
              margin-bottom: 0;
              border-width: 1px 0 0;
            }
            .navbar-brand {
              float: left;
              padding: 15px 15px;
              font-size: 18px;
              line-height: 20px;
              height: 50px;
            }
            .navbar-brand:hover,
            .navbar-brand:focus {
              text-decoration: none;
            }
            @media (min-width: 768px) {
              .navbar > .container .navbar-brand,
              .navbar > .container-fluid .navbar-brand {
                margin-left: -15px;
              }
            }
            .navbar-toggle {
              position: relative;
              float: right;
              margin-right: 15px;
              padding: 9px 10px;
              margin-top: 8px;
              margin-bottom: 8px;
              background-color: transparent;
              background-image: none;
              border: 1px solid transparent;
              border-radius: 4px;
            }
            .navbar-toggle:focus {
              outline: 0;
            }
            .navbar-toggle .icon-bar {
              display: block;
              width: 22px;
              height: 2px;
              border-radius: 1px;
            }
            .navbar-toggle .icon-bar + .icon-bar {
              margin-top: 4px;
            }
            @media (min-width: 768px) {
              .navbar-toggle {
                display: none;
              }
            }
            .navbar-nav {
              margin: 7.5px -15px;
            }
            .navbar-nav > li > a {
              padding-top: 10px;
              padding-bottom: 10px;
              line-height: 20px;
            }
            @media (max-width: 767px) {
              .navbar-nav .open .dropdown-menu {
                position: static;
                float: none;
                width: auto;
                margin-top: 0;
                background-color: transparent;
                border: 0;
                box-shadow: none;
              }
              .navbar-nav .open .dropdown-menu > li > a,
              .navbar-nav .open .dropdown-menu .dropdown-header {
                padding: 5px 15px 5px 25px;
              }
              .navbar-nav .open .dropdown-menu > li > a {
                line-height: 20px;
              }
              .navbar-nav .open .dropdown-menu > li > a:hover,
              .navbar-nav .open .dropdown-menu > li > a:focus {
                background-image: none;
              }
            }
            @media (min-width: 768px) {
              .navbar-nav {
                float: left;
                margin: 0;
              }
              .navbar-nav > li {
                float: left;
              }
              .navbar-nav > li > a {
                padding-top: 15px;
                padding-bottom: 15px;
              }
              .navbar-nav.navbar-right:last-child {
                margin-right: -15px;
              }
            }
            @media (min-width: 768px) {
              .navbar-left {
                float: left !important;
              }
              .navbar-right {
                float: right !important;
              }
            }
            .navbar-form {
              margin-left: -15px;
              margin-right: -15px;
              padding: 10px 15px;
              border-top: 1px solid transparent;
              border-bottom: 1px solid transparent;
              -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1), 0 1px 0 rgba(255, 255, 255, 0.1);
              box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1), 0 1px 0 rgba(255, 255, 255, 0.1);
              margin-top: 8px;
              margin-bottom: 8px;
            }
            @media (min-width: 768px) {
              .navbar-form .form-group {
                display: inline-block;
                margin-bottom: 0;
                vertical-align: middle;
              }
              .navbar-form .form-control {
                display: inline-block;
                width: auto;
                vertical-align: middle;
              }
              .navbar-form .input-group {
                display: inline-table;
                vertical-align: middle;
              }
              .navbar-form .input-group .input-group-addon,
              .navbar-form .input-group .input-group-btn,
              .navbar-form .input-group .form-control {
                width: auto;
              }
              .navbar-form .input-group > .form-control {
                width: 100%;
              }
              .navbar-form .control-label {
                margin-bottom: 0;
                vertical-align: middle;
              }
              .navbar-form .radio,
              .navbar-form .checkbox {
                display: inline-block;
                margin-top: 0;
                margin-bottom: 0;
                vertical-align: middle;
              }
              .navbar-form .radio label,
              .navbar-form .checkbox label {
                padding-left: 0;
              }
              .navbar-form .radio input[type="radio"],
              .navbar-form .checkbox input[type="checkbox"] {
                position: relative;
                margin-left: 0;
              }
              .navbar-form .has-feedback .form-control-feedback {
                top: 0;
              }
            }
            @media (max-width: 767px) {
              .navbar-form .form-group {
                margin-bottom: 5px;
              }
            }
            @media (min-width: 768px) {
              .navbar-form {
                width: auto;
                border: 0;
                margin-left: 0;
                margin-right: 0;
                padding-top: 0;
                padding-bottom: 0;
                -webkit-box-shadow: none;
                box-shadow: none;
              }
              .navbar-form.navbar-right:last-child {
                margin-right: -15px;
              }
            }
            .navbar-nav > li > .dropdown-menu {
              margin-top: 0;
              border-top-right-radius: 0;
              border-top-left-radius: 0;
            }
            .navbar-fixed-bottom .navbar-nav > li > .dropdown-menu {
              border-bottom-right-radius: 0;
              border-bottom-left-radius: 0;
            }
            .navbar-btn {
              margin-top: 8px;
              margin-bottom: 8px;
            }
            .navbar-btn.btn-sm {
              margin-top: 10px;
              margin-bottom: 10px;
            }
            .navbar-btn.btn-xs {
              margin-top: 14px;
              margin-bottom: 14px;
            }
            .navbar-text {
              margin-top: 15px;
              margin-bottom: 15px;
            }
            @media (min-width: 768px) {
              .navbar-text {
                float: left;
                margin-left: 15px;
                margin-right: 15px;
              }
              .navbar-text.navbar-right:last-child {
                margin-right: 0;
              }
            }
            .navbar-default {
              background-color: #f8f8f8;
              border-color: #e7e7e7;
            }
            .navbar-default .navbar-brand {
              color: #777777;
            }
            .navbar-default .navbar-brand:hover,
            .navbar-default .navbar-brand:focus {
              color: #5e5e5e;
              background-color: transparent;
            }
            .navbar-default .navbar-text {
              color: #777777;
            }
            .navbar-default .navbar-nav > li > a {
              color: #777777;
            }
            .navbar-default .navbar-nav > li > a:hover,
            .navbar-default .navbar-nav > li > a:focus {
              color: #333333;
              background-color: transparent;
            }
            .navbar-default .navbar-nav > .active > a,
            .navbar-default .navbar-nav > .active > a:hover,
            .navbar-default .navbar-nav > .active > a:focus {
              color: #555555;
              background-color: #e7e7e7;
            }
            .navbar-default .navbar-nav > .disabled > a,
            .navbar-default .navbar-nav > .disabled > a:hover,
            .navbar-default .navbar-nav > .disabled > a:focus {
              color: #cccccc;
              background-color: transparent;
            }
            .navbar-default .navbar-toggle {
              border-color: #dddddd;
            }
            .navbar-default .navbar-toggle:hover,
            .navbar-default .navbar-toggle:focus {
              background-color: #dddddd;
            }
            .navbar-default .navbar-toggle .icon-bar {
              background-color: #888888;
            }
            .navbar-default .navbar-collapse,
            .navbar-default .navbar-form {
              border-color: #e7e7e7;
            }
            .navbar-default .navbar-nav > .open > a,
            .navbar-default .navbar-nav > .open > a:hover,
            .navbar-default .navbar-nav > .open > a:focus {
              background-color: #e7e7e7;
              color: #555555;
            }
            @media (max-width: 767px) {
              .navbar-default .navbar-nav .open .dropdown-menu > li > a {
                color: #777777;
              }
              .navbar-default .navbar-nav .open .dropdown-menu > li > a:hover,
              .navbar-default .navbar-nav .open .dropdown-menu > li > a:focus {
                color: #333333;
                background-color: transparent;
              }
              .navbar-default .navbar-nav .open .dropdown-menu > .active > a,
              .navbar-default .navbar-nav .open .dropdown-menu > .active > a:hover,
              .navbar-default .navbar-nav .open .dropdown-menu > .active > a:focus {
                color: #555555;
                background-color: #e7e7e7;
              }
              .navbar-default .navbar-nav .open .dropdown-menu > .disabled > a,
              .navbar-default .navbar-nav .open .dropdown-menu > .disabled > a:hover,
              .navbar-default .navbar-nav .open .dropdown-menu > .disabled > a:focus {
                color: #cccccc;
                background-color: transparent;
              }
            }
            .navbar-default .navbar-link {
              color: #777777;
            }
            .navbar-default .navbar-link:hover {
              color: #333333;
            }
            .navbar-default .btn-link {
              color: #777777;
            }
            .navbar-default .btn-link:hover,
            .navbar-default .btn-link:focus {
              color: #333333;
            }
            .navbar-default .btn-link[disabled]:hover,
            fieldset[disabled] .navbar-default .btn-link:hover,
            .navbar-default .btn-link[disabled]:focus,
            fieldset[disabled] .navbar-default .btn-link:focus {
              color: #cccccc;
            }
            .navbar-inverse {
              background-color: #222222;
              border-color: #080808;
            }
            .navbar-inverse .navbar-brand {
              color: #777777;
            }
            .navbar-inverse .navbar-brand:hover,
            .navbar-inverse .navbar-brand:focus {
              color: #ffffff;
              background-color: transparent;
            }
            .navbar-inverse .navbar-text {
              color: #777777;
            }
            .navbar-inverse .navbar-nav > li > a {
              color: #777777;
            }
            .navbar-inverse .navbar-nav > li > a:hover,
            .navbar-inverse .navbar-nav > li > a:focus {
              color: #ffffff;
              background-color: transparent;
            }
            .navbar-inverse .navbar-nav > .active > a,
            .navbar-inverse .navbar-nav > .active > a:hover,
            .navbar-inverse .navbar-nav > .active > a:focus {
              color: #ffffff;
              background-color: #080808;
            }
            .navbar-inverse .navbar-nav > .disabled > a,
            .navbar-inverse .navbar-nav > .disabled > a:hover,
            .navbar-inverse .navbar-nav > .disabled > a:focus {
              color: #444444;
              background-color: transparent;
            }
            .navbar-inverse .navbar-toggle {
              border-color: #333333;
            }
            .navbar-inverse .navbar-toggle:hover,
            .navbar-inverse .navbar-toggle:focus {
              background-color: #333333;
            }
            .navbar-inverse .navbar-toggle .icon-bar {
              background-color: #ffffff;
            }
            .navbar-inverse .navbar-collapse,
            .navbar-inverse .navbar-form {
              border-color: #101010;
            }
            .navbar-inverse .navbar-nav > .open > a,
            .navbar-inverse .navbar-nav > .open > a:hover,
            .navbar-inverse .navbar-nav > .open > a:focus {
              background-color: #080808;
              color: #ffffff;
            }
            @media (max-width: 767px) {
              .navbar-inverse .navbar-nav .open .dropdown-menu > .dropdown-header {
                border-color: #080808;
              }
              .navbar-inverse .navbar-nav .open .dropdown-menu .divider {
                background-color: #080808;
              }
              .navbar-inverse .navbar-nav .open .dropdown-menu > li > a {
                color: #777777;
              }
              .navbar-inverse .navbar-nav .open .dropdown-menu > li > a:hover,
              .navbar-inverse .navbar-nav .open .dropdown-menu > li > a:focus {
                color: #ffffff;
                background-color: transparent;
              }
              .navbar-inverse .navbar-nav .open .dropdown-menu > .active > a,
              .navbar-inverse .navbar-nav .open .dropdown-menu > .active > a:hover,
              .navbar-inverse .navbar-nav .open .dropdown-menu > .active > a:focus {
                color: #ffffff;
                background-color: #080808;
              }
              .navbar-inverse .navbar-nav .open .dropdown-menu > .disabled > a,
              .navbar-inverse .navbar-nav .open .dropdown-menu > .disabled > a:hover,
              .navbar-inverse .navbar-nav .open .dropdown-menu > .disabled > a:focus {
                color: #444444;
                background-color: transparent;
              }
            }
            .navbar-inverse .navbar-link {
              color: #777777;
            }
            .navbar-inverse .navbar-link:hover {
              color: #ffffff;
            }
            .navbar-inverse .btn-link {
              color: #777777;
            }
            .navbar-inverse .btn-link:hover,
            .navbar-inverse .btn-link:focus {
              color: #ffffff;
            }
            .navbar-inverse .btn-link[disabled]:hover,
            fieldset[disabled] .navbar-inverse .btn-link:hover,
            .navbar-inverse .btn-link[disabled]:focus,
            fieldset[disabled] .navbar-inverse .btn-link:focus {
              color: #444444;
            }
            .jumbotron {
              padding: 30px;
              margin-bottom: 30px;
              color: inherit;
              background-color: #eeeeee;
            }
            .jumbotron h1,
            .jumbotron .h1 {
              color: inherit;
            }
            .jumbotron p {
              margin-bottom: 15px;
              font-size: 21px;
              font-weight: 200;
            }
            .jumbotron > hr {
              border-top-color: #d5d5d5;
            }
            .container .jumbotron {
              border-radius: 6px;
            }
            .jumbotron .container {
              max-width: 100%;
            }
            @media screen and (min-width: 768px) {
              .jumbotron {
                padding-top: 48px;
                padding-bottom: 48px;
              }
              .container .jumbotron {
                padding-left: 60px;
                padding-right: 60px;
              }
              .jumbotron h1,
              .jumbotron .h1 {
                font-size: 63px;
              }
            }
            .list-group {
              margin-bottom: 20px;
              padding-left: 0;
            }
            .list-group-item {
              position: relative;
              display: block;
              padding: 10px 15px;
              margin-bottom: -1px;
              background-color: #ffffff;
              border: 1px solid #dddddd;
            }
            .list-group-item:first-child {
              border-top-right-radius: 4px;
              border-top-left-radius: 4px;
            }
            .list-group-item:last-child {
              margin-bottom: 0;
              border-bottom-right-radius: 4px;
              border-bottom-left-radius: 4px;
            }
            .list-group-item > .badge {
              float: right;
            }
            .list-group-item > .badge + .badge {
              margin-right: 5px;
            }
            a.list-group-item {
              color: #555555;
            }
            a.list-group-item .list-group-item-heading {
              color: #333333;
            }
            a.list-group-item:hover,
            a.list-group-item:focus {
              text-decoration: none;
              color: #555555;
              background-color: #f5f5f5;
            }
            .list-group-item.disabled,
            .list-group-item.disabled:hover,
            .list-group-item.disabled:focus {
              background-color: #eeeeee;
              color: #777777;
            }
            .list-group-item.disabled .list-group-item-heading,
            .list-group-item.disabled:hover .list-group-item-heading,
            .list-group-item.disabled:focus .list-group-item-heading {
              color: inherit;
            }
            .list-group-item.disabled .list-group-item-text,
            .list-group-item.disabled:hover .list-group-item-text,
            .list-group-item.disabled:focus .list-group-item-text {
              color: #777777;
            }
            .list-group-item.active,
            .list-group-item.active:hover,
            .list-group-item.active:focus {
              z-index: 2;
              color: #ffffff;
              background-color: #428bca;
              border-color: #428bca;
            }
            .list-group-item.active .list-group-item-heading,
            .list-group-item.active:hover .list-group-item-heading,
            .list-group-item.active:focus .list-group-item-heading,
            .list-group-item.active .list-group-item-heading > small,
            .list-group-item.active:hover .list-group-item-heading > small,
            .list-group-item.active:focus .list-group-item-heading > small,
            .list-group-item.active .list-group-item-heading > .small,
            .list-group-item.active:hover .list-group-item-heading > .small,
            .list-group-item.active:focus .list-group-item-heading > .small {
              color: inherit;
            }
            .list-group-item.active .list-group-item-text,
            .list-group-item.active:hover .list-group-item-text,
            .list-group-item.active:focus .list-group-item-text {
              color: #e1edf7;
            }
            .list-group-item-success {
              color: #3c763d;
              background-color: #dff0d8;
            }
            a.list-group-item-success {
              color: #3c763d;
            }
            a.list-group-item-success .list-group-item-heading {
              color: inherit;
            }
            a.list-group-item-success:hover,
            a.list-group-item-success:focus {
              color: #3c763d;
              background-color: #d0e9c6;
            }
            a.list-group-item-success.active,
            a.list-group-item-success.active:hover,
            a.list-group-item-success.active:focus {
              color: #fff;
              background-color: #3c763d;
              border-color: #3c763d;
            }
            .list-group-item-info {
              color: #31708f;
              background-color: #d9edf7;
            }
            a.list-group-item-info {
              color: #31708f;
            }
            a.list-group-item-info .list-group-item-heading {
              color: inherit;
            }
            a.list-group-item-info:hover,
            a.list-group-item-info:focus {
              color: #31708f;
              background-color: #c4e3f3;
            }
            a.list-group-item-info.active,
            a.list-group-item-info.active:hover,
            a.list-group-item-info.active:focus {
              color: #fff;
              background-color: #31708f;
              border-color: #31708f;
            }
            .list-group-item-warning {
              color: #8a6d3b;
              background-color: #fcf8e3;
            }
            a.list-group-item-warning {
              color: #8a6d3b;
            }
            a.list-group-item-warning .list-group-item-heading {
              color: inherit;
            }
            a.list-group-item-warning:hover,
            a.list-group-item-warning:focus {
              color: #8a6d3b;
              background-color: #faf2cc;
            }
            a.list-group-item-warning.active,
            a.list-group-item-warning.active:hover,
            a.list-group-item-warning.active:focus {
              color: #fff;
              background-color: #8a6d3b;
              border-color: #8a6d3b;
            }
            .list-group-item-danger {
              color: #a94442;
              background-color: #f2dede;
            }
            a.list-group-item-danger {
              color: #a94442;
            }
            a.list-group-item-danger .list-group-item-heading {
              color: inherit;
            }
            a.list-group-item-danger:hover,
            a.list-group-item-danger:focus {
              color: #a94442;
              background-color: #ebcccc;
            }
            a.list-group-item-danger.active,
            a.list-group-item-danger.active:hover,
            a.list-group-item-danger.active:focus {
              color: #fff;
              background-color: #a94442;
              border-color: #a94442;
            }
            .list-group-item-heading {
              margin-top: 0;
              margin-bottom: 5px;
            }
            .list-group-item-text {
              margin-bottom: 0;
              line-height: 1.3;
            }
            .panel {
              margin-bottom: 20px;
              background-color: #ffffff;
              border: 1px solid transparent;
              border-radius: 4px;
              -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
              box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
            }
            .panel-body {
              padding: 15px;
            }
            .panel-heading {
              padding: 10px 15px;
              border-bottom: 1px solid transparent;
              border-top-right-radius: 3px;
              border-top-left-radius: 3px;
            }
            .panel-heading > .dropdown .dropdown-toggle {
              color: inherit;
            }
            .panel-title {
              margin-top: 0;
              margin-bottom: 0;
              font-size: 16px;
              color: inherit;
            }
            .panel-title > a {
              color: inherit;
            }
            .panel-footer {
              padding: 10px 15px;
              background-color: #f5f5f5;
              border-top: 1px solid #dddddd;
              border-bottom-right-radius: 3px;
              border-bottom-left-radius: 3px;
            }
            .panel > .list-group {
              margin-bottom: 0;
            }
            .panel > .list-group .list-group-item {
              border-width: 1px 0;
              border-radius: 0;
            }
            .panel > .list-group:first-child .list-group-item:first-child {
              border-top: 0;
              border-top-right-radius: 3px;
              border-top-left-radius: 3px;
            }
            .panel > .list-group:last-child .list-group-item:last-child {
              border-bottom: 0;
              border-bottom-right-radius: 3px;
              border-bottom-left-radius: 3px;
            }
            .panel-heading + .list-group .list-group-item:first-child {
              border-top-width: 0;
            }
            .list-group + .panel-footer {
              border-top-width: 0;
            }
            .panel > .table,
            .panel > .table-responsive > .table,
            .panel > .panel-collapse > .table {
              margin-bottom: 0;
            }
            .panel > .table:first-child,
            .panel > .table-responsive:first-child > .table:first-child {
              border-top-right-radius: 3px;
              border-top-left-radius: 3px;
            }
            .panel > .table:first-child > thead:first-child > tr:first-child td:first-child,
            .panel > .table-responsive:first-child > .table:first-child > thead:first-child > tr:first-child td:first-child,
            .panel > .table:first-child > tbody:first-child > tr:first-child td:first-child,
            .panel > .table-responsive:first-child > .table:first-child > tbody:first-child > tr:first-child td:first-child,
            .panel > .table:first-child > thead:first-child > tr:first-child th:first-child,
            .panel > .table-responsive:first-child > .table:first-child > thead:first-child > tr:first-child th:first-child,
            .panel > .table:first-child > tbody:first-child > tr:first-child th:first-child,
            .panel > .table-responsive:first-child > .table:first-child > tbody:first-child > tr:first-child th:first-child {
              border-top-left-radius: 3px;
            }
            .panel > .table:first-child > thead:first-child > tr:first-child td:last-child,
            .panel > .table-responsive:first-child > .table:first-child > thead:first-child > tr:first-child td:last-child,
            .panel > .table:first-child > tbody:first-child > tr:first-child td:last-child,
            .panel > .table-responsive:first-child > .table:first-child > tbody:first-child > tr:first-child td:last-child,
            .panel > .table:first-child > thead:first-child > tr:first-child th:last-child,
            .panel > .table-responsive:first-child > .table:first-child > thead:first-child > tr:first-child th:last-child,
            .panel > .table:first-child > tbody:first-child > tr:first-child th:last-child,
            .panel > .table-responsive:first-child > .table:first-child > tbody:first-child > tr:first-child th:last-child {
              border-top-right-radius: 3px;
            }
            .panel > .table:last-child,
            .panel > .table-responsive:last-child > .table:last-child {
              border-bottom-right-radius: 3px;
              border-bottom-left-radius: 3px;
            }
            .panel > .table:last-child > tbody:last-child > tr:last-child td:first-child,
            .panel > .table-responsive:last-child > .table:last-child > tbody:last-child > tr:last-child td:first-child,
            .panel > .table:last-child > tfoot:last-child > tr:last-child td:first-child,
            .panel > .table-responsive:last-child > .table:last-child > tfoot:last-child > tr:last-child td:first-child,
            .panel > .table:last-child > tbody:last-child > tr:last-child th:first-child,
            .panel > .table-responsive:last-child > .table:last-child > tbody:last-child > tr:last-child th:first-child,
            .panel > .table:last-child > tfoot:last-child > tr:last-child th:first-child,
            .panel > .table-responsive:last-child > .table:last-child > tfoot:last-child > tr:last-child th:first-child {
              border-bottom-left-radius: 3px;
            }
            .panel > .table:last-child > tbody:last-child > tr:last-child td:last-child,
            .panel > .table-responsive:last-child > .table:last-child > tbody:last-child > tr:last-child td:last-child,
            .panel > .table:last-child > tfoot:last-child > tr:last-child td:last-child,
            .panel > .table-responsive:last-child > .table:last-child > tfoot:last-child > tr:last-child td:last-child,
            .panel > .table:last-child > tbody:last-child > tr:last-child th:last-child,
            .panel > .table-responsive:last-child > .table:last-child > tbody:last-child > tr:last-child th:last-child,
            .panel > .table:last-child > tfoot:last-child > tr:last-child th:last-child,
            .panel > .table-responsive:last-child > .table:last-child > tfoot:last-child > tr:last-child th:last-child {
              border-bottom-right-radius: 3px;
            }
            .panel > .panel-body + .table,
            .panel > .panel-body + .table-responsive {
              border-top: 1px solid #dddddd;
            }
            .panel > .table > tbody:first-child > tr:first-child th,
            .panel > .table > tbody:first-child > tr:first-child td {
              border-top: 0;
            }
            .panel > .table-bordered,
            .panel > .table-responsive > .table-bordered {
              border: 0;
            }
            .panel > .table-bordered > thead > tr > th:first-child,
            .panel > .table-responsive > .table-bordered > thead > tr > th:first-child,
            .panel > .table-bordered > tbody > tr > th:first-child,
            .panel > .table-responsive > .table-bordered > tbody > tr > th:first-child,
            .panel > .table-bordered > tfoot > tr > th:first-child,
            .panel > .table-responsive > .table-bordered > tfoot > tr > th:first-child,
            .panel > .table-bordered > thead > tr > td:first-child,
            .panel > .table-responsive > .table-bordered > thead > tr > td:first-child,
            .panel > .table-bordered > tbody > tr > td:first-child,
            .panel > .table-responsive > .table-bordered > tbody > tr > td:first-child,
            .panel > .table-bordered > tfoot > tr > td:first-child,
            .panel > .table-responsive > .table-bordered > tfoot > tr > td:first-child {
              border-left: 0;
            }
            .panel > .table-bordered > thead > tr > th:last-child,
            .panel > .table-responsive > .table-bordered > thead > tr > th:last-child,
            .panel > .table-bordered > tbody > tr > th:last-child,
            .panel > .table-responsive > .table-bordered > tbody > tr > th:last-child,
            .panel > .table-bordered > tfoot > tr > th:last-child,
            .panel > .table-responsive > .table-bordered > tfoot > tr > th:last-child,
            .panel > .table-bordered > thead > tr > td:last-child,
            .panel > .table-responsive > .table-bordered > thead > tr > td:last-child,
            .panel > .table-bordered > tbody > tr > td:last-child,
            .panel > .table-responsive > .table-bordered > tbody > tr > td:last-child,
            .panel > .table-bordered > tfoot > tr > td:last-child,
            .panel > .table-responsive > .table-bordered > tfoot > tr > td:last-child {
              border-right: 0;
            }
            .panel > .table-bordered > thead > tr:first-child > td,
            .panel > .table-responsive > .table-bordered > thead > tr:first-child > td,
            .panel > .table-bordered > tbody > tr:first-child > td,
            .panel > .table-responsive > .table-bordered > tbody > tr:first-child > td,
            .panel > .table-bordered > thead > tr:first-child > th,
            .panel > .table-responsive > .table-bordered > thead > tr:first-child > th,
            .panel > .table-bordered > tbody > tr:first-child > th,
            .panel > .table-responsive > .table-bordered > tbody > tr:first-child > th {
              border-bottom: 0;
            }
            .panel > .table-bordered > tbody > tr:last-child > td,
            .panel > .table-responsive > .table-bordered > tbody > tr:last-child > td,
            .panel > .table-bordered > tfoot > tr:last-child > td,
            .panel > .table-responsive > .table-bordered > tfoot > tr:last-child > td,
            .panel > .table-bordered > tbody > tr:last-child > th,
            .panel > .table-responsive > .table-bordered > tbody > tr:last-child > th,
            .panel > .table-bordered > tfoot > tr:last-child > th,
            .panel > .table-responsive > .table-bordered > tfoot > tr:last-child > th {
              border-bottom: 0;
            }
            .panel > .table-responsive {
              border: 0;
              margin-bottom: 0;
            }
            .panel-group {
              margin-bottom: 20px;
            }
            .panel-group .panel {
              margin-bottom: 0;
              border-radius: 4px;
            }
            .panel-group .panel + .panel {
              margin-top: 5px;
            }
            .panel-group .panel-heading {
              border-bottom: 0;
            }
            .panel-group .panel-heading + .panel-collapse > .panel-body {
              border-top: 1px solid #dddddd;
            }
            .panel-group .panel-footer {
              border-top: 0;
            }
            .panel-group .panel-footer + .panel-collapse .panel-body {
              border-bottom: 1px solid #dddddd;
            }
            .panel-default {
              border-color: #dddddd;
            }
            .panel-default > .panel-heading {
              color: #333333;
              background-color: #f5f5f5;
              border-color: #dddddd;
            }
            .panel-default > .panel-heading + .panel-collapse > .panel-body {
              border-top-color: #dddddd;
            }
            .panel-default > .panel-heading .badge {
              color: #f5f5f5;
              background-color: #333333;
            }
            .panel-default > .panel-footer + .panel-collapse > .panel-body {
              border-bottom-color: #dddddd;
            }
            .panel-primary {
              border-color: #428bca;
            }
            .panel-primary > .panel-heading {
              color: #ffffff;
              background-color: #428bca;
              border-color: #428bca;
            }
            .panel-primary > .panel-heading + .panel-collapse > .panel-body {
              border-top-color: #428bca;
            }
            .panel-primary > .panel-heading .badge {
              color: #428bca;
              background-color: #ffffff;
            }
            .panel-primary > .panel-footer + .panel-collapse > .panel-body {
              border-bottom-color: #428bca;
            }
            .panel-success {
              border-color: #d6e9c6;
            }
            .panel-success > .panel-heading {
              color: #3c763d;
              background-color: #dff0d8;
              border-color: #d6e9c6;
            }
            .panel-success > .panel-heading + .panel-collapse > .panel-body {
              border-top-color: #d6e9c6;
            }
            .panel-success > .panel-heading .badge {
              color: #dff0d8;
              background-color: #3c763d;
            }
            .panel-success > .panel-footer + .panel-collapse > .panel-body {
              border-bottom-color: #d6e9c6;
            }
            .panel-info {
              border-color: #bce8f1;
            }
            .panel-info > .panel-heading {
              color: #31708f;
              background-color: #d9edf7;
              border-color: #bce8f1;
            }
            .panel-info > .panel-heading + .panel-collapse > .panel-body {
              border-top-color: #bce8f1;
            }
            .panel-info > .panel-heading .badge {
              color: #d9edf7;
              background-color: #31708f;
            }
            .panel-info > .panel-footer + .panel-collapse > .panel-body {
              border-bottom-color: #bce8f1;
            }
            .panel-warning {
              border-color: #faebcc;
            }
            .panel-warning > .panel-heading {
              color: #8a6d3b;
              background-color: #fcf8e3;
              border-color: #faebcc;
            }
            .panel-warning > .panel-heading + .panel-collapse > .panel-body {
              border-top-color: #faebcc;
            }
            .panel-warning > .panel-heading .badge {
              color: #fcf8e3;
              background-color: #8a6d3b;
            }
            .panel-warning > .panel-footer + .panel-collapse > .panel-body {
              border-bottom-color: #faebcc;
            }
            .panel-danger {
              border-color: #ebccd1;
            }
            .panel-danger > .panel-heading {
              color: #a94442;
              background-color: #f2dede;
              border-color: #ebccd1;
            }
            .panel-danger > .panel-heading + .panel-collapse > .panel-body {
              border-top-color: #ebccd1;
            }
            .panel-danger > .panel-heading .badge {
              color: #f2dede;
              background-color: #a94442;
            }
            .panel-danger > .panel-footer + .panel-collapse > .panel-body {
              border-bottom-color: #ebccd1;
            }
            .well {
              min-height: 20px;
              padding: 19px;
              margin-bottom: 20px;
              background-color: #f5f5f5;
              border: 1px solid #e3e3e3;
              border-radius: 4px;
              -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
              box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
            }
            .well blockquote {
              border-color: #ddd;
              border-color: rgba(0, 0, 0, 0.15);
            }
            .well-lg {
              padding: 24px;
              border-radius: 6px;
            }
            .well-sm {
              padding: 9px;
              border-radius: 3px;
            }
            .clearfix:before,
            .clearfix:after,
            .container:before,
            .container:after,
            .container-fluid:before,
            .container-fluid:after,
            .row:before,
            .row:after,
            .form-horizontal .form-group:before,
            .form-horizontal .form-group:after,
            .nav:before,
            .nav:after,
            .navbar:before,
            .navbar:after,
            .navbar-header:before,
            .navbar-header:after,
            .navbar-collapse:before,
            .navbar-collapse:after,
            .panel-body:before,
            .panel-body:after {
              content: " ";
              display: table;
            }
            .clearfix:after,
            .container:after,
            .container-fluid:after,
            .row:after,
            .form-horizontal .form-group:after,
            .nav:after,
            .navbar:after,
            .navbar-header:after,
            .navbar-collapse:after,
            .panel-body:after {
              clear: both;
            }
            .center-block {
              display: block;
              margin-left: auto;
              margin-right: auto;
            }
            .pull-right {
              float: right !important;
            }
            .pull-left {
              float: left !important;
            }
            .hide {
              display: none !important;
            }
            .show {
              display: block !important;
            }
            .invisible {
              visibility: hidden;
            }
            .text-hide {
              font: 0/0 a;
              color: transparent;
              text-shadow: none;
              background-color: transparent;
              border: 0;
            }
            .hidden {
              display: none !important;
              visibility: hidden !important;
            }
            .affix {
              position: fixed;
              -webkit-transform: translate3d(0, 0, 0);
              transform: translate3d(0, 0, 0);
            }
            @-ms-viewport {
              width: device-width;
            }
            .visible-xs,
            .visible-sm,
            .visible-md,
            .visible-lg {
              display: none !important;
            }
            .visible-xs-block,
            .visible-xs-inline,
            .visible-xs-inline-block,
            .visible-sm-block,
            .visible-sm-inline,
            .visible-sm-inline-block,
            .visible-md-block,
            .visible-md-inline,
            .visible-md-inline-block,
            .visible-lg-block,
            .visible-lg-inline,
            .visible-lg-inline-block {
              display: none !important;
            }
            @media (max-width: 767px) {
              .visible-xs {
                display: block !important;
              }
              table.visible-xs {
                display: table;
              }
              tr.visible-xs {
                display: table-row !important;
              }
              th.visible-xs,
              td.visible-xs {
                display: table-cell !important;
              }
            }
            @media (max-width: 767px) {
              .visible-xs-block {
                display: block !important;
              }
            }
            @media (max-width: 767px) {
              .visible-xs-inline {
                display: inline !important;
              }
            }
            @media (max-width: 767px) {
              .visible-xs-inline-block {
                display: inline-block !important;
              }
            }
            @media (min-width: 768px) and (max-width: 991px) {
              .visible-sm {
                display: block !important;
              }
              table.visible-sm {
                display: table;
              }
              tr.visible-sm {
                display: table-row !important;
              }
              th.visible-sm,
              td.visible-sm {
                display: table-cell !important;
              }
            }
            @media (min-width: 768px) and (max-width: 991px) {
              .visible-sm-block {
                display: block !important;
              }
            }
            @media (min-width: 768px) and (max-width: 991px) {
              .visible-sm-inline {
                display: inline !important;
              }
            }
            @media (min-width: 768px) and (max-width: 991px) {
              .visible-sm-inline-block {
                display: inline-block !important;
              }
            }
            @media (min-width: 992px) and (max-width: 1199px) {
              .visible-md {
                display: block !important;
              }
              table.visible-md {
                display: table;
              }
              tr.visible-md {
                display: table-row !important;
              }
              th.visible-md,
              td.visible-md {
                display: table-cell !important;
              }
            }
            @media (min-width: 992px) and (max-width: 1199px) {
              .visible-md-block {
                display: block !important;
              }
            }
            @media (min-width: 992px) and (max-width: 1199px) {
              .visible-md-inline {
                display: inline !important;
              }
            }
            @media (min-width: 992px) and (max-width: 1199px) {
              .visible-md-inline-block {
                display: inline-block !important;
              }
            }
            @media (min-width: 1200px) {
              .visible-lg {
                display: block !important;
              }
              table.visible-lg {
                display: table;
              }
              tr.visible-lg {
                display: table-row !important;
              }
              th.visible-lg,
              td.visible-lg {
                display: table-cell !important;
              }
            }
            @media (min-width: 1200px) {
              .visible-lg-block {
                display: block !important;
              }
            }
            @media (min-width: 1200px) {
              .visible-lg-inline {
                display: inline !important;
              }
            }
            @media (min-width: 1200px) {
              .visible-lg-inline-block {
                display: inline-block !important;
              }
            }
            @media (max-width: 767px) {
              .hidden-xs {
                display: none !important;
              }
            }
            @media (min-width: 768px) and (max-width: 991px) {
              .hidden-sm {
                display: none !important;
              }
            }
            @media (min-width: 992px) and (max-width: 1199px) {
              .hidden-md {
                display: none !important;
              }
            }
            @media (min-width: 1200px) {
              .hidden-lg {
                display: none !important;
              }
            }
            .visible-print {
              display: none !important;
            }
            @media print {
              .visible-print {
                display: block !important;
              }
              table.visible-print {
                display: table;
              }
              tr.visible-print {
                display: table-row !important;
              }
              th.visible-print,
              td.visible-print {
                display: table-cell !important;
              }
            }
            .visible-print-block {
              display: none !important;
            }
            @media print {
              .visible-print-block {
                display: block !important;
              }
            }
            .visible-print-inline {
              display: none !important;
            }
            @media print {
              .visible-print-inline {
                display: inline !important;
              }
            }
            .visible-print-inline-block {
              display: none !important;
            }
            @media print {
              .visible-print-inline-block {
                display: inline-block !important;
              }
            }
            @media print {
              .hidden-print {
                display: none !important;
              }
            }

        </style>
    <style>
        .list-group-item.active{
            z-index: 2;
            color: #fff;
            background-color: #428bca;
            border-color: #428bca;
        }
    </style>
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

</head>

<body>

<!-- Fixed navbar -->
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="/" style="padding:10px;">
                <img src="{{ isset($message) ? $message->embed($logo) : '' }}" style="height:35px;"/>
            </a>
        </div>
    </div>
</div>

<div class="container" style="margin-top: 70px;">
    <h2>Wysłano prośbę o wystawienie faktury:</h2>
    <div class="row" style="margin-top: 20px;">
        <div class="col-sm-12">
            <ul class="list-group">
                <li class="list-group-item">Data zapłaty: <b>{{ $wreck->payment }}</b></li>
            </ul>
        </div>
        <div class="col-sm-12">
            <ul class="list-group">
                <li class="list-group-item active">Dane wraku</li>
                <li class="list-group-item">Wartość pojazdu do odkupu przez LB: <b>{{ $wreck->value_repurchase }} zł</b></li>
                <li class="list-group-item">Cena dla oferenta aukcyjnego: <b>{{ $wreck->value_tenderer }} zł</b></li>
                <li class="list-group-item">Data ważności oferty oferenta aukcyjnego: <b>{{ $wreck->expire_tenderer }}</b></li>
                <li class="list-group-item">Wartość pojazdu w stanie nieuszkodzonym: <b>{{ $wreck->value_undamaged }} zł</b></li>
                <li class="list-group-item">Numer aukcyjny ubezpieczalni: <b>{{ $wreck->nr_auction }}</b></li>
                <li class="list-group-item">Nabywca: <b>{{ Config::get('definition.wreck_buyers.'.$wreck->buyer) }}</b></li>
                <li class="list-group-item">Cena odkupu: <b>{{ $wreck->repurchase_price }} zł</b></li>
            </ul>
        </div>
        <div class="col-sm-12">
            <ul class="list-group">
                <li class="list-group-item active">Dane nabywcy</li>
                <li class="list-group-item">Nazwa nabywcy: <b>{{ $wreck->buyer_name }}</b></li>
                <li class="list-group-item">Adres: <b>{{ $wreck->buyer_address_street.' '.$wreck->buyer_address_code.' '.$wreck->buyer_address_city }}</b></li>
                <li class="list-group-item">Nip: <b>{{ $wreck->buyer_nip }}</b></li>
                <li class="list-group-item">REGON: <b>{{ $wreck->buyer_regon }}</b></li>
                <li class="list-group-item">Email: <b>{{ $wreck->buyer_email }}</b></li>
                <li class="list-group-item">Telefon: <b>{{ $wreck->buyer_phone }}</b></li>
                <li class="list-group-item">Osoba kontaktowa: <b>{{ $wreck->buyer_contact_person }}</b></li>
            </ul>
        </div>
        <div class="col-sm-12">
            <ul class="list-group">
              <li class="list-group-item active">Dane pojazdu</li>
              <li class="list-group-item">Nr umowy leasingowej: <b>{{ $vehicle->nr_contract }}</b></li>
              <li class="list-group-item">Nr rejestracyjny pojazdu: <b>{{ $vehicle->registration }}</b></li>
              <li class="list-group-item">Nr VIN pojazdu: <b>{{ $vehicle->VIN }}</b></li>
            </ul>
        </div>
    </div>
</div> <!-- /container -->


<!-- Javascripts	================================================== -->

{{ HTML::script('assets/js/jquery-1.10.2.js') }}
{{ HTML::script('assets/js/bootstrap.min.js') }}



</body>
</html>