## 1. Backend API Implementation

- [x] 1.1 Review and harden FarmController with proper validation
- [x] 1.2 Add unique farm name validation per user in store/update
- [x] 1.3 Ensure ownership authorization on update/destroy
- [x] 1.4 Add consistent error envelopes for all failure modes
- [x] 1.5 Verify routes are correctly registered in api.php

## 2. Backend Tests

- [x] 2.1 Test farm list returns user's farms with role
- [x] 2.2 Test farm list returns empty array when no farms
- [x] 2.3 Test farm creation with valid data
- [x] 2.4 Test farm creation with duplicate name
- [x] 2.5 Test farm creation without name
- [x] 2.6 Test farm retrieval by ID
- [x] 2.7 Test farm retrieval for non-member farm returns 404
- [x] 2.8 Test farm update as owner
- [x] 2.9 Test farm update as non-owner returns 403
- [x] 2.10 Test farm update with duplicate name
- [x] 2.11 Test farm deletion as owner
- [x] 2.12 Test farm deletion as non-owner returns 403
- [x] 2.13 Test farm deletion removes pivot records

## 3. Bruno API Collection

- [x] 3.1 Add farm CRUD requests to Bruno collection
- [x] 3.2 Add environment variables for farm testing
- [x] 3.3 Add post-response scripts for farm ID extraction

## 4. Documentation

- [x] 4.1 Update specs/farm-crud/spec.md with final requirements
- [x] 4.2 Verify all scenarios have corresponding tests
- [x] 4.3 Update sprint-progress.md when complete
