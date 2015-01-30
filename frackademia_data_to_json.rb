require 'csv'
require 'json'
require 'pry'

fields = {
  'Title' => 'title',
  'Date' => 'date',
  'Issuing Org' => 'org',
  'Type of Organization Issuing' => 'org_type',
  'Topic' => 'topic',
  'Peer reviewed' => 'peer_reviewed',
  'Strength of Industry Ties' => 'ties_strength',
  'Type of Industry Ties' => 'ties_type',
  'Duplicate?' => 'duplicate',
  'Notes on Industry Ties' => 'ties_notes',
  'Other Notes' => 'other_notes',
  'Authors' => 'authors',
  'Organizations' => 'orgs',
  'Link' => 'link'
}

data = []
line = 0

CSV.foreach('data/frackademia-data.csv') do |row|
  if line == 0
    line = 1
    next
  end

  obj = {}

  fields.values.each_with_index do |value, i|
    obj[value] = row[i] ? row[i] : ""
  end

  data << obj
  line += 1
end

open('data/frackademia-data.json', 'wb') do |file|
  file << data.to_json
end