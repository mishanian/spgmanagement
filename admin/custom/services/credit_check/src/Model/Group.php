<?php
/**
 * Group
 *
 * PHP version 5
 *
 * @category Class
 * @package  DocuSign\eSign
 * @author   http://github.com/swagger-api/swagger-codegen
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache Licene v2
 * @link     https://github.com/swagger-api/swagger-codegen
 */
/**
 *  Copyright 2016 SmartBear Software
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */
/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace DocuSign\eSign\Model;

use \ArrayAccess;
/**
 * Group Class Doc Comment
 *
 * @category    Class
 * @description 
 * @package     DocuSign\eSign
 * @author      http://github.com/swagger-api/swagger-codegen
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache Licene v2
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class Group implements ArrayAccess
{
    /**
      * Array of property to type mappings. Used for (de)serialization 
      * @var string[]
      */
    static $swaggerTypes = array(
        'group_id' => 'string',
        'group_name' => 'string',
        'permission_profile_id' => 'string',
        'group_type' => 'string',
        'users' => '\DocuSign\eSign\Model\UserInfo[]',
        'error_details' => '\DocuSign\eSign\Model\ErrorDetails'
    );
  
    /** 
      * Array of attributes where the key is the local name, and the value is the original name
      * @var string[] 
      */
    static $attributeMap = array(
        'group_id' => 'groupId',
        'group_name' => 'groupName',
        'permission_profile_id' => 'permissionProfileId',
        'group_type' => 'groupType',
        'users' => 'users',
        'error_details' => 'errorDetails'
    );
  
    /**
      * Array of attributes to setter functions (for deserialization of responses)
      * @var string[]
      */
    static $setters = array(
        'group_id' => 'setGroupId',
        'group_name' => 'setGroupName',
        'permission_profile_id' => 'setPermissionProfileId',
        'group_type' => 'setGroupType',
        'users' => 'setUsers',
        'error_details' => 'setErrorDetails'
    );
  
    /**
      * Array of attributes to getter functions (for serialization of requests)
      * @var string[]
      */
    static $getters = array(
        'group_id' => 'getGroupId',
        'group_name' => 'getGroupName',
        'permission_profile_id' => 'getPermissionProfileId',
        'group_type' => 'getGroupType',
        'users' => 'getUsers',
        'error_details' => 'getErrorDetails'
    );
  
    
    /**
      * $group_id The DocuSign group ID for the group.
      * @var string
      */
    protected $group_id;
    
    /**
      * $group_name The name of the group.
      * @var string
      */
    protected $group_name;
    
    /**
      * $permission_profile_id The ID of the permission profile associated with the group.
      * @var string
      */
    protected $permission_profile_id;
    
    /**
      * $group_type The group type.
      * @var string
      */
    protected $group_type;
    
    /**
      * $users 
      * @var \DocuSign\eSign\Model\UserInfo[]
      */
    protected $users;
    
    /**
      * $error_details 
      * @var \DocuSign\eSign\Model\ErrorDetails
      */
    protected $error_details;
    

    /**
     * Constructor
     * @param mixed[] $data Associated array of property value initalizing the model
     */
    public function __construct(array $data = null)
    {
        if ($data != null) {
            $this->group_id = $data["group_id"];
            $this->group_name = $data["group_name"];
            $this->permission_profile_id = $data["permission_profile_id"];
            $this->group_type = $data["group_type"];
            $this->users = $data["users"];
            $this->error_details = $data["error_details"];
        }
    }
    
    /**
     * Gets group_id
     * @return string
     */
    public function getGroupId()
    {
        return $this->group_id;
    }
  
    /**
     * Sets group_id
     * @param string $group_id The DocuSign group ID for the group.
     * @return $this
     */
    public function setGroupId($group_id)
    {
        
        $this->group_id = $group_id;
        return $this;
    }
    
    /**
     * Gets group_name
     * @return string
     */
    public function getGroupName()
    {
        return $this->group_name;
    }
  
    /**
     * Sets group_name
     * @param string $group_name The name of the group.
     * @return $this
     */
    public function setGroupName($group_name)
    {
        
        $this->group_name = $group_name;
        return $this;
    }
    
    /**
     * Gets permission_profile_id
     * @return string
     */
    public function getPermissionProfileId()
    {
        return $this->permission_profile_id;
    }
  
    /**
     * Sets permission_profile_id
     * @param string $permission_profile_id The ID of the permission profile associated with the group.
     * @return $this
     */
    public function setPermissionProfileId($permission_profile_id)
    {
        
        $this->permission_profile_id = $permission_profile_id;
        return $this;
    }
    
    /**
     * Gets group_type
     * @return string
     */
    public function getGroupType()
    {
        return $this->group_type;
    }
  
    /**
     * Sets group_type
     * @param string $group_type The group type.
     * @return $this
     */
    public function setGroupType($group_type)
    {
        
        $this->group_type = $group_type;
        return $this;
    }
    
    /**
     * Gets users
     * @return \DocuSign\eSign\Model\UserInfo[]
     */
    public function getUsers()
    {
        return $this->users;
    }
  
    /**
     * Sets users
     * @param \DocuSign\eSign\Model\UserInfo[] $users 
     * @return $this
     */
    public function setUsers($users)
    {
        
        $this->users = $users;
        return $this;
    }
    
    /**
     * Gets error_details
     * @return \DocuSign\eSign\Model\ErrorDetails
     */
    public function getErrorDetails()
    {
        return $this->error_details;
    }
  
    /**
     * Sets error_details
     * @param \DocuSign\eSign\Model\ErrorDetails $error_details 
     * @return $this
     */
    public function setErrorDetails($error_details)
    {
        
        $this->error_details = $error_details;
        return $this;
    }
    
    /**
     * Returns true if offset exists. False otherwise.
     * @param  integer $offset Offset 
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }
  
    /**
     * Gets offset.
     * @param  integer $offset Offset 
     * @return mixed 
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }
  
    /**
     * Sets value based on offset.
     * @param  integer $offset Offset 
     * @param  mixed   $value  Value to be set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }
  
    /**
     * Unsets offset.
     * @param  integer $offset Offset 
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }
  
    /**
     * Gets the string presentation of the object
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) {
            return json_encode(\DocuSign\eSign\ObjectSerializer::sanitizeForSerialization($this), JSON_PRETTY_PRINT);
        } else {
            return json_encode(\DocuSign\eSign\ObjectSerializer::sanitizeForSerialization($this));
        }
    }
}
