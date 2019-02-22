import expect from 'expect'
import generalContractor from '../../resources/assets/js/classes/GeneralContractor'

describe('generalContractor', () => {

  it('should create new user', function() {
    let form = new SparkForm({
      phone: "6023508801",
      customerName: "Kristen",
      jobName: "Massage"
    });

    let disabled = { submit: false };
    expect(generalContractor.prototype.initiateBid(form, disabled)).toBe('hello');
  })

})