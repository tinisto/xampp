#!/usr/bin/env python3

import os

# Create directories for remaining tests
tests_to_create = [
    'english-test',
    'history-test', 
    'astronomy-test',
    'emotional-intelligence-test'
]

base_path = '/Applications/XAMPP/xamppfiles/htdocs/pages/tests'

for test in tests_to_create:
    test_dir = os.path.join(base_path, test)
    os.makedirs(test_dir, exist_ok=True)
    print(f"✓ Created directory: {test}")

print("\nAll test directories created!")