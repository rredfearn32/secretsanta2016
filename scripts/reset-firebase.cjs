function argsAreValid(args) {
  if (args.length !== 1) {
    console.log('Invalid arguments provided');
    return false;
  }

  const validEnvArgs = ['users', 'uses2'];
  if (!validEnvArgs.includes(args[0])) {
    console.log(
      `Invalid env args provided. Env args must be one of: [${validEnvArgs.join(
        ', ',
      )}]`,
    );
    return false;
  }

  return true;
}

(async () => {
  const args = process.argv.slice(2);

  if (!argsAreValid(args)) return;

  const admin = require('firebase-admin');
  const config = require('./config.cjs');

  const targetApp = admin.initializeApp(
    {
      credential: admin.credential.cert(config),
      databaseURL: `https://secretsanta-3255.firebaseio.com`,
    },
    'targetApp',
  );
  const targetDb = targetApp.firestore();

  const collectionName = args[0];
  const targetCollectionSnapshot = await targetDb
    .collection(collectionName)
    .get();

  const results = [];
  targetCollectionSnapshot.docs.map(async (doc) => {
    const modifiedDoc = {
      ...doc.data(),
      choiceMade: false,
      chosen: false,
    };

    delete modifiedDoc.chosenPerson;

    results.push(modifiedDoc);

    targetDb.collection(collectionName).doc(doc.id).set(modifiedDoc);
  });

  console.log('Modified the following docs: ');
  console.log(results);
})();
