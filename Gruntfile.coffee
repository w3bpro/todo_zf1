# read configs
globalConfig = {}

# force "bare: true" option for snockets
CoffeeScript = require 'coffee-script'


# calculating MD5 sums
fs = require 'fs'
md5 = require 'MD5'
calculateMd5String = (path) ->
	'-' + md5 fs.readFileSync path

module.exports = (grunt) ->
	grunt.loadNpmTasks 'grunt-contrib-concat'
	grunt.loadNpmTasks 'grunt-replace'
	grunt.loadNpmTasks 'grunt-contrib-less'
	grunt.loadNpmTasks 'grunt-contrib-cssmin'
	grunt.loadNpmTasks 'grunt-contrib-watch'
	grunt.loadNpmTasks 'grunt-contrib-uglify'
	grunt.loadNpmTasks 'grunt-shell'

	grunt.initConfig
		pkg: grunt.file.readJSON 'package.json'
		globalConfig: globalConfig

		concat:
			app:
				files:
					'public/js/libs.js' : [
						'public/js/libs/*.js'									
					]
		less:
			app:
				files:
					'public/css/style.css': 'less/load.less'
		cssmin:
			app:
				options:
					banner: '/* <%= grunt.template.today() %> */\n'
				files:
					'public/css/style.css': 'public/css/style.css'

		watch:
			less:
				files: ['less/**/*.less', 'public/js/site/*.js']
				tasks: ['less', 'concat']

		uglify:
			app:
				options:
					banner: '/* <%= grunt.template.today() %> */\n'
				dest: 'public/js/app.js'
				src: ['public/js/app.js']

	grunt.registerTask 'md5', ->
		updateHashes = (path) ->
			src = grunt.file.read path
			Object.keys(patterns).forEach (key) ->
				re = new RegExp('\<\<'+key+'\>\>', 'g')
				src = src.replace re, patterns[key]
			grunt.file.write path, src

		patterns = {
			#'md5.style.css': calculateMd5String 'public/css/style.css'
		}

	grunt.registerTask 'default', ->
		globalConfig.env = 'dev'
		grunt.task.run ['less', 'concat', 'md5', 'watch']
		


 	