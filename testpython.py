#!/usr/bin/env python
import cgi

field = cgi.FieldStorage()
print "Content-Type: text/plain\n\n"
print 'Hello, world!\n'