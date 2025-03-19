
# Parse useful text from USAgov static site html files into files containing
# the relevant data from each html file, without the html stuff - just a line
# of text for each relevant html element


from bs4 import BeautifulSoup
from pathlib import Path
import re
import os

skip_text = [
    "Find an office near you:",
    "Contact:",
    "Website:",
    "Phone number:",
    "Ask a real person any government-related question for free. They will get you the answer or let you know where to find it.",
    "SHARE THIS PAGE:",
    "LAST UPDATED:" ]

# Name the css classes which are most likely to contain useful text
css_classes = ['usa-prose', 'usa-card__body', 'life-events-item-content',
               'usagov-directory-table']

root_path = '.'
in_path = root_path + '/input'
out_path = root_path + '/output'

# recursively get the path of each html file we want to process for input
html_files = []
for root, _, files in os.walk(in_path):
    for file in files:
        if file.endswith(".html"):
            html_files.append(os.path.join(root, file))

# iterate over each of the input html files, extracting and writing what we
# need into a corresponding output file
for html_file in html_files:
    with open(html_file, 'r', encoding='utf-8') as file:
        html_cont = file.read()

    # each static file in USAgov is named index.html, and lives
    # in an appropriately named directory.  We want to take that
    # directory name, and use it as the filename for the output
    # file, rather than having a directory per output file
    fin_path, output_file = os.path.split(html_file)
    path_parts = Path(fin_path).parts
    page_path = path_parts[-1]
    output_file, _ = os.path.splitext(output_file)
    output_file = out_path + '/' + page_path + '.dat'

    # ping user
    print("Processing to '" + output_file + "'")

    items_written = 0
    # open the receiving file, before we start processing the input file
    check_duplicates = []
    with open(output_file, 'w', encoding='utf-8') as ofile:
        soup = BeautifulSoup(html_cont, 'html.parser')

        divs = soup.find_all('div', class_=css_classes)
        for div in divs:
            # find all the sub-elements we're interested in
            paragraphs = div.find_all('p')
            spans = div.find_all('span')
            hrefs = div.find_all('href')
            items_of_interest = paragraphs + spans + hrefs

            # iterate over each sub-element
            for i in items_of_interest:
                s = i.get_text()
                if s not in check_duplicates:
                    check_duplicates.append(s)
                    # remove boilerplate text
                    process = True
                    for skip in skip_text:
                        if s.startswith(skip):
                            process = False
                    if process:
                        # cleanup whitespace, etc
                        s = re.sub(r"\s+|\r+|\n+|\t+", " ", s)
                        s.join(s.split())
                        # write to output file
                        print(s, file=ofile)
                        items_written += 1

    # close output file before proceeding to the next input file
    ofile.close()
    if items_written == 0:
        print("No data written for '" + output_file + "'. Removing file")
        os.remove(output_file)
