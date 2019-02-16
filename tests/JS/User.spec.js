import expect from 'expect'
import User from '../../resources/assets/js/classes/User'

describe('User', () => {

  it('should say hello', function() {
    console.log(User.prototype.hello());
    expect(User.prototype.hello()).toBe('world');
  })

  it('should return success when contractor info is sent to be registered', function() {

  })

})