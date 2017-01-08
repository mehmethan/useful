#!/usr/bin/env python3

import argparse
from bs4 import BeautifulSoup
import urllib.request
import urllib.parse
import os
import json
from lxml import etree

def get_page_html(url, raw_html = False):
	try:
		html = urllib.request.urlopen(url).read()
		if raw_html:
			return html

		html = BeautifulSoup(html, 'lxml')
		return html
	except Exception as e:
		print("Url Error : " + url)
		return None


def find_urls(html, base_url):
	links = []

	try:
		urls = [a['href'] for a in html.find_all('a', href = True) if a['href'] != '#' and a['href'] != '/']
		#fix list, exclude foreign urls
		for url in urls:
			parsed_url = urllib.parse.urlparse(url)
			if parsed_url.netloc in base_url:
				if parsed_url.netloc == '':
					url = base_url + parsed_url.path

				links.append(url)

	except Exception as e:
		raise e

	return links

def create_xml_output(urls):
	root = etree.Element('urlset')
	for u in urls:
		url = etree.Element('url')
		loc = etree.Element('loc')
		loc.text = u
		url.append(loc)
		root.append(url)

	sm = etree.tostring(root)
	f = open("sitemap.xml", "w")
	f.write(str(sm.decode("utf-8")))
	f.close()

def create_json_output(urls):
	output = [{"url":url} for url in urls]
	json_o = json.dumps(output)
	f = open("sitemap.json", 'w')
	f.write(json_o)
	f.close()



def main():
	parser = argparse.ArgumentParser()
	parser.add_argument('--url', help="Website url to crawl")
	parser.add_argument('--format', default="xml", help="Output format (Xml/Json)")
	args = vars(parser.parse_args())

	base_url = args['url']
	output_type = args['format']

	url_crawled = []
	url_found = [base_url,]

	for url in url_found:
		if url not in url_crawled:
			html = get_page_html(url)
			if html != None:
				link_list = find_urls(html, base_url)
				for link in link_list:
					if link not in url_found:
						url_found.append(link)
				url_crawled.append(url)


	if output_type == "json":
		create_json_output(url_found)
	elif output_type == "xml":
		create_xml_output(url_found)
	else:
		print("Output type must be xml or json")




if __name__ == "__main__":
	main()
